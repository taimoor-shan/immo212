<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Facades\EmailHandler;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\AccountProjectForm;
use Botble\RealEstate\Http\Requests\AccountProjectRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\AccountActivityLog;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Services\StoreProjectCategoryService;
use Botble\RealEstate\Tables\AccountProjectTable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AccountProjectController extends BaseController
{
    public function __construct()
    {
        OptimizerHelper::disable();
    }

    public function index(AccountProjectTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::project.name'));

        return $table->render('plugins/real-estate::account.table.base');
    }

    public function create()
    {
        if (! auth('account')->user()->canPost()) {
            return redirect()->back()->with(['error_msg' => trans('plugins/real-estate::package.add_credit_alert')]);
        }

        $this->pageTitle(trans('plugins/real-estate::project.create'));

        return AccountProjectForm::create()->renderForm();
    }

    public function store(
        AccountProjectRequest $request,
        StoreProjectCategoryService $projectCategoryService,
        SaveFacilitiesService $saveFacilitiesService
    ) {
        if (! auth('account')->user()->canPost()) {
            return redirect()->back()->with(['error_msg' => trans('plugins/real-estate::package.add_credit_alert')]);
        }

        $projectForm = AccountProjectForm::create()->setRequest($request);

        $projectForm->saving(function (AccountProjectForm $form) use ($projectCategoryService, $saveFacilitiesService): void {
            $request = $form->getRequest();

            /** @var Project $project */
            $project = $form->getModel();

            $project->fill(array_merge($request->input(), [
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ]));

            // Set expiry based on admin-configured days (same helper as properties)
            if (Schema::hasColumn('re_projects', 'expire_date')) {
                $project->expire_date = Carbon::now()->addDays(RealEstateHelper::propertyExpiredDays());
            }
            if (Schema::hasColumn('re_projects', 'never_expired')) {
                $project->never_expired = false; // Users cannot set never-expired
            }

            $enabledPostApproval = (bool) setting('enable_post_approval', 1);

            if (! $enabledPostApproval) {
                $project->moderation_status = ModerationStatusEnum::APPROVED;
            }

            $project->save();

            $form->fireModelEvents($project);

            $project->features()->sync($request->input('features', []));
            $saveFacilitiesService->execute($project, $request->input('facilities', []));
            $projectCategoryService->execute($request, $project);

            // If post approval is enabled, send notification email like properties
            if ((bool) setting('enable_post_approval', 1)) {
                EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'post_name' => $project->name,
                        'post_url' => route('project.edit', $project->id),
                        'post_author' => $project->author->name ?? '',
                    ])
                    ->sendUsingTemplate('new-pending-property');
            }

            AccountActivityLog::query()->create([
                'action' => 'create_project',
                'reference_name' => $project->name,
                'reference_url' => route('public.account.projects.edit', $project->id),
            ]);

            if (RealEstateHelper::isEnabledCreditsSystem()) {
                $account = Account::query()->findOrFail(auth('account')->id());
                $account->credits--;
                $account->save();
            }
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('public.account.projects.index'))
            ->setNextUrl(route('public.account.projects.edit', $projectForm->getModel()->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(int|string $id)
    {
        $project = Project::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])
            ->firstOrFail();

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $project->name]));

        return AccountProjectForm::createFromModel($project)->renderForm();
    }

    public function update(
        int|string $id,
        AccountProjectRequest $request,
        StoreProjectCategoryService $projectCategoryService,
        SaveFacilitiesService $saveFacilitiesService
    ) {
        $project = Project::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])->firstOrFail();

        $projectForm = AccountProjectForm::createFromModel($project)->setRequest($request);

        $projectForm->saving(function (AccountProjectForm $form) use ($projectCategoryService, $saveFacilitiesService): void {
            $request = $form->getRequest();

            /** @var Project $project */
            $project = $form->getModel();

            $project->fill($request->input());
            if (Schema::hasColumn('re_projects', 'never_expired')) {
                $project->never_expired = false; // Users cannot set never-expired
            }
            $enabledPostApproval = (bool) setting('enable_post_approval', 1);

            if (! $enabledPostApproval) {
                $project->moderation_status = ModerationStatusEnum::APPROVED;
            }

            $project->save();

            $form->fireModelEvents($project);

            $project->features()->sync($request->input('features', []));
            $saveFacilitiesService->execute($project, $request->input('facilities', []));
            $projectCategoryService->execute($request, $project);

            AccountActivityLog::query()->create([
                'action' => 'update_project',
                'reference_name' => $project->name,
                'reference_url' => route('public.account.projects.edit', $project->id),
            ]);
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('public.account.projects.index'))
            ->setNextUrl(route('public.account.projects.edit', $project->id))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(int|string $id)
    {
        $project = Project::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])
            ->firstOrFail();

        AccountActivityLog::query()->create([
            'action' => 'delete_project',
            'reference_name' => $project->name,
        ]);

        return DeleteResourceAction::make($project);
    }

    public function renew(int|string $id): JsonResponse
    {
        $project = Project::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])->firstOrFail();

        $account = auth('account')->user();

        if (RealEstateHelper::isEnabledCreditsSystem() && $account->credits < 1) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage("You don't have enough credit to renew this project!");
        }

        if (Schema::hasColumn('re_projects', 'expire_date')) {
            $project->expire_date = $project->expire_date
                ? Carbon::parse($project->expire_date)->addDays(RealEstateHelper::propertyExpiredDays())
                : Carbon::now()->addDays(RealEstateHelper::propertyExpiredDays());
            $project->save();
        }

        if (RealEstateHelper::isEnabledCreditsSystem()) {
            $account->credits--;
            $account->save();
        }

        return $this->httpResponse()->setMessage(__('Renew project successfully'));
    }
}
