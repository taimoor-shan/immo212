<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Facades\EmailHandler;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HiddenField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Rules\MediaImageRule;
use Botble\Media\Facades\RvMedia;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\AccountVacationRentalForm;
use Botble\RealEstate\Http\Requests\AccountVacationRentalRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\AccountActivityLog;
use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Services\SaveVacationRentalAvailabilityService;
use Botble\RealEstate\Services\StorePropertyCategoryService;
use Botble\RealEstate\Tables\AccountVacationRentalTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AccountVacationRentalController extends BaseController
{
    public function __construct()
    {
        OptimizerHelper::disable();
    }

    public function index(AccountVacationRentalTable $vacationRentalTable)
    {
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.vacation_rentals'));

        return $vacationRentalTable->render('plugins/real-estate::account.table.base');
    }

    public function create(Request $request)
    {
        if (! auth('account')->user()->canPost()) {
            return redirect()->back()->with(['error_msg' => trans('plugins/real-estate::package.add_credit_alert')]);
        }

        $this->pageTitle(trans('plugins/real-estate::vacation-rental.create'));

        return AccountVacationRentalForm::create()
            ->disablePermalinkField(! setting('allow_customizing_post_url', true))
            ->add('is_slug_editable', HiddenField::class, TextFieldOption::make()->value(1))
            ->renderForm();
    }

    public function store(
        AccountVacationRentalRequest $request,
        StorePropertyCategoryService $propertyCategoryService,
        SaveFacilitiesService $saveFacilitiesService,
        SaveVacationRentalAvailabilityService $saveVacationRentalAvailabilityService
    ) {
        if (! auth('account')->user()->canPost()) {
            return redirect()->back()->with(['error_msg' => trans('plugins/real-estate::package.add_credit_alert')]);
        }

        $vacationRentalForm = AccountVacationRentalForm::create()->setRequest($request);

        $vacationRentalForm->saving(function (AccountVacationRentalForm $form) use (
            $propertyCategoryService,
            $saveFacilitiesService,
            $saveVacationRentalAvailabilityService
        ): void {
            $request = $form->getRequest();

            /**
             * @var VacationRental $vacationRental
             */
            $vacationRental = $form->getModel();

            $vacationRental->fill(array_merge($this->processRequestData($request), [
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ]));

            $vacationRental->expire_date = Carbon::now()->addDays(RealEstateHelper::propertyExpiredDays());
            $vacationRental->never_expired = false; // Users cannot create never-expiring properties

            $enabledPostApproval = (bool) setting('enable_post_approval', 1);

            if (! $enabledPostApproval && $vacationRental->status != PropertyStatusEnum::DRAFT) {
                $vacationRental->moderation_status = ModerationStatusEnum::APPROVED;
            }

            $vacationRental->save();

            $vacationRental->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($vacationRental, $request->input('facilities', []));

            $saveVacationRentalAvailabilityService->execute($vacationRental, $request->input('availability_data', []));

            $propertyCategoryService->execute($request, $vacationRental);

            $form->fireModelEvents($vacationRental);

            AccountActivityLog::query()->create([
                'action' => 'create_vacation_rental',
                'reference_name' => $vacationRental->name,
                'reference_url' => route('public.account.vacation-rentals.edit', $vacationRental->id),
            ]);

            if (RealEstateHelper::isEnabledCreditsSystem()) {
                $account = Account::query()->findOrFail(auth('account')->id());
                $account->credits--;
                $account->save();
            }

            if ($enabledPostApproval && $vacationRental->status != PropertyStatusEnum::DRAFT) {
                EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'post_name' => $vacationRental->name,
                        'post_url' => route('vacation-rental.edit', $vacationRental->id),
                        'post_author' => $vacationRental->author->name,
                    ])
                    ->sendUsingTemplate('new-pending-vacation-rental');
            }
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('public.account.vacation-rentals.index'))
            ->setNextUrl(route('public.account.vacation-rentals.edit', $vacationRentalForm->getModel()->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(int|string $id)
    {
        $vacationRental = VacationRental::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])
            ->firstOrFail();

        $this->pageTitle(trans('plugins/real-estate::vacation-rental.edit') . ' "' . $vacationRental->name . '"');

        return AccountVacationRentalForm::createFromModel($vacationRental)
            ->disablePermalinkField($isDisabledPermalinkField = ! setting('allow_customizing_post_url', true))
            ->when($isDisabledPermalinkField, function (AccountVacationRentalForm $form): AccountVacationRentalForm {
                return $form
                    ->modify(
                        'name',
                        TextField::class,
                        NameFieldOption::make()
                            ->required()
                            ->helperText($form->getModel()->url . '?preview=true'),
                        true
                    );
            })
            ->renderForm();
    }

    public function update(
        int|string $id,
        AccountVacationRentalRequest $request,
        StorePropertyCategoryService $propertyCategoryService,
        SaveFacilitiesService $saveFacilitiesService,
        SaveVacationRentalAvailabilityService $saveVacationRentalAvailabilityService
    ) {
        $vacationRental = VacationRental::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])
            ->firstOrFail();

        $vacationRentalForm = AccountVacationRentalForm::createFromModel($vacationRental)->setRequest($request);

        $vacationRentalForm->saving(function (AccountVacationRentalForm $form) use (
            $propertyCategoryService,
            $saveFacilitiesService,
            $saveVacationRentalAvailabilityService
        ): void {
            $request = $form->getRequest();

            /**
             * @var VacationRental $vacationRental
             */
            $vacationRental = $form->getModel();

            $vacationRental->fill($this->processRequestData($request));
            $vacationRental->never_expired = false; // Users cannot create never-expiring properties

            $enabledPostApproval = (bool) setting('enable_post_approval', 1);

            if (! $enabledPostApproval && $vacationRental->status != PropertyStatusEnum::DRAFT) {
                $vacationRental->moderation_status = ModerationStatusEnum::APPROVED;
            }

            $vacationRental->save();

            $form->fireModelEvents($vacationRental);

            $vacationRental->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($vacationRental, $request->input('facilities', []));

            $saveVacationRentalAvailabilityService->execute($vacationRental, $request->input('availability_data', []));

            $propertyCategoryService->execute($request, $vacationRental);

            AccountActivityLog::query()->create([
                'action' => 'update_vacation_rental',
                'reference_name' => $vacationRental->name,
                'reference_url' => route('public.account.vacation-rentals.edit', $vacationRental->id),
            ]);

            if ($enabledPostApproval && $vacationRental->status != PropertyStatusEnum::DRAFT) {
                EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'post_name' => $vacationRental->name,
                        'post_url' => route('vacation-rental.edit', $vacationRental->id),
                        'post_author' => $vacationRental->author->name,
                    ])
                    ->sendUsingTemplate('new-pending-vacation-rental');
            }
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('public.account.vacation-rentals.index'))
            ->setNextUrl(route('public.account.vacation-rentals.edit', $vacationRental->id))
            ->withUpdatedSuccessMessage();
    }

    protected function processRequestData(Request $request): array
    {
        $shortcodeCompiler = shortcode()->getCompiler();

        $request->merge([
            'content' => $shortcodeCompiler->strip($request->input('content'), $shortcodeCompiler->whitelistShortcodes()),
        ]);

        $except = [
            'is_featured',
            'author_id',
            'author_type',
            'expire_date',
            'never_expired',
            'moderation_status',
        ];

        foreach ($except as $item) {
            $request->request->remove($item);
        }

        return $request->input();
    }

    public function destroy(int|string $id)
    {
        $vacationRental = VacationRental::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])
            ->firstOrFail();

        AccountActivityLog::query()->create([
            'action' => 'delete_vacation_rental',
            'reference_name' => $vacationRental->name,
        ]);

        return DeleteResourceAction::make($vacationRental);
    }

    public function renew(int|string $id)
    {
        $vacationRental = VacationRental::query()->findOrFail($id);

        $account = auth('account')->user();

        if (RealEstateHelper::isEnabledCreditsSystem() && $account->credits < 1) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__("You don't have enough credit to renew this vacation rental!"));
        }

        $vacationRental->expire_date = $vacationRental->expire_date->addDays(RealEstateHelper::propertyExpiredDays());
        $vacationRental->save();

        if (RealEstateHelper::isEnabledCreditsSystem()) {
            $account->credits--;
            $account->save();
        }

        return $this
            ->httpResponse()
            ->setMessage(__('Renew vacation rental successfully'));
    }
}
