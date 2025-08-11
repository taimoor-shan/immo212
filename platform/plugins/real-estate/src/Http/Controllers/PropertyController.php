<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\PropertyForm;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Services\SavePropertyAvailabilityService;
use Botble\RealEstate\Services\SavePropertyCustomFieldService;
use Botble\RealEstate\Services\StorePropertyCategoryService;
use Botble\RealEstate\Tables\PropertyTable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PropertyController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::property.name'), route('property.index'));
    }

    public function index(PropertyTable $dataTable)
    {
        $this->pageTitle(trans('plugins/real-estate::property.name'));

        return $dataTable->renderTable();
    }

    public function create(Request $request)
    {
        $this->pageTitle(trans('plugins/real-estate::property.create'));

        $form = PropertyForm::create();

        // Pre-populate project_id if provided
        if ($projectId = $request->get('project_id')) {
            $project = \Botble\RealEstate\Models\Project::find($projectId);
            if ($project) {
                $property = new \Botble\RealEstate\Models\Property(['project_id' => $projectId]);
                $form->setModel($property);
                $this->breadcrumb()->add($project->name, route('project.edit', $project->id));

                // Add hidden field to track that this property is being created from project
                if ($request->get('from_project')) {
                    $form->add('from_project', 'hidden', ['value' => 1]);
                }
            }
        }

        return $form->renderForm();
    }

    public function store(
        PropertyRequest $request,
        StorePropertyCategoryService $propertyCategoryService,
        SaveFacilitiesService $saveFacilitiesService,
        SavePropertyCustomFieldService $savePropertyCustomFieldService,
        SavePropertyAvailabilityService $savePropertyAvailabilityService
    ) {
        $request->merge([
            'expire_date' => Carbon::now()->addDays(RealEstateHelper::propertyExpiredDays()),
            'images' => array_filter($request->input('images', [])),
            'author_type' => Account::class,
        ]);

        $propertyForm = PropertyForm::create()->setRequest($request);

        $propertyForm->saving(function (PropertyForm $form) use ($propertyCategoryService, $saveFacilitiesService, $savePropertyCustomFieldService, $savePropertyAvailabilityService): void {
            $request = $form->getRequest();

            /**
             * @var Property $property
             */
            $property = $form->getModel();
            $property->fill($request->input());
            $property->moderation_status = ModerationStatusEnum::APPROVED;
            $property->never_expired = $request->input('never_expired');
            $property->save();

            $form->fireModelEvents($property);

            if (RealEstateHelper::isEnabledCustomFields()) {
                $savePropertyCustomFieldService->execute($property, $request->input('custom_fields', []));
            }

            $property->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($property, $request->input('facilities', []));
            $savePropertyAvailabilityService->execute($property, $request->input('availability_data', []));
            $propertyCategoryService->execute($request, $property);
        });

        $property = $propertyForm->getModel();

        // If property was created from project context, redirect back to project
        if ($property->project_id && $request->has('from_project')) {
            return $this
                ->httpResponse()
                ->setPreviousUrl(route('project.edit', $property->project_id))
                ->setNextUrl(route('project.edit', $property->project_id))
                ->setMessage(trans('plugins/real-estate::property.created_successfully') . ' ' . trans('plugins/real-estate::property.redirecting_to_project'))
                ->withCreatedSuccessMessage();
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property.index'))
            ->setNextUrl(route('property.edit', $property->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(int|string $id, Request $request = null)
    {
        \Log::info('=== PROPERTY CONTROLLER EDIT CALLED ===', [
            'property_id' => $id,
            'request_method' => $request ? $request->method() : 'GET',
            'request_url' => $request ? $request->url() : 'N/A',
            'request_full_url' => $request ? $request->fullUrl() : 'N/A',
            'is_post' => $request ? $request->isMethod('POST') : false,
            'has_availability_data' => $request ? $request->has('availability_data') : false,
            'availability_data_keys' => $request && $request->has('availability_data') ? array_keys($request->input('availability_data', [])) : [],
            'availability_data_content' => $request ? $request->input('availability_data', []) : [],
            'all_input_keys' => $request ? array_keys($request->all()) : [],
            'content_type' => $request ? $request->header('Content-Type') : 'N/A',
            'user_agent' => $request ? $request->header('User-Agent') : 'N/A'
        ]);

        // If this is a POST request, it's a form submission - call the update method
        if ($request && $request->isMethod('POST')) {
            \Log::info('=== POST REQUEST TO EDIT - CALLING UPDATE METHOD ===', [
                'property_id' => $id,
                'form_data_present' => $request->has('name') || $request->has('description'),
                'availability_data_present' => $request->has('availability_data')
            ]);

            // Convert the request to PropertyRequest and call update
            $propertyRequest = app(PropertyRequest::class);
            $propertyRequest->replace($request->all());
            $propertyRequest->setMethod('POST');

            return $this->update(
                $id,
                $propertyRequest,
                app(StorePropertyCategoryService::class),
                app(SaveFacilitiesService::class),
                app(SavePropertyCustomFieldService::class),
                app(SavePropertyAvailabilityService::class)
            );
        }

        /**
         * @var Property $property
         */
        $property = Property::query()->with(['features', 'author', 'project'])->findOrFail($id);

        Assets::addScriptsDirectly(['vendor/core/plugins/real-estate/js/duplicate-property.js']);

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $property->name]));

        // Add project breadcrumb if property belongs to a project
        if ($property->project_id && $property->project) {
            $this->breadcrumb()->add($property->project->name, route('project.edit', $property->project->id));
        }

        return PropertyForm::createFromModel($property)->renderForm();
    }

    public function update(
        int|string $id,
        PropertyRequest $request,
        StorePropertyCategoryService $propertyCategoryService,
        SaveFacilitiesService $saveFacilitiesService,
        SavePropertyCustomFieldService $savePropertyCustomFieldService,
        SavePropertyAvailabilityService $savePropertyAvailabilityService
    ) {
        \Log::info('=== PROPERTY CONTROLLER UPDATE CALLED ===', [
            'property_id' => $id,
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_full_url' => $request->fullUrl(),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
            'has_availability_data' => $request->has('availability_data'),
            'availability_data_keys' => $request->has('availability_data') ? array_keys($request->input('availability_data', [])) : [],
            'availability_data_raw' => $request->input('availability_data', []),
            'all_input_keys' => array_keys($request->all()),
            'form_fields_present' => [
                'name' => $request->has('name'),
                'description' => $request->has('description'),
                'type' => $request->has('type'),
                'features' => $request->has('features'),
                'facilities' => $request->has('facilities'),
                'availability_data' => $request->has('availability_data')
            ]
        ]);

        // Detailed availability data logging
        if ($request->has('availability_data')) {
            $availabilityData = $request->input('availability_data', []);
            \Log::info('=== AVAILABILITY DATA DETAILED ANALYSIS ===', [
                'blocked_dates_present' => isset($availabilityData['blocked_dates']),
                'blocked_dates_content' => $availabilityData['blocked_dates'] ?? null,
                'blocked_dates_type' => isset($availabilityData['blocked_dates']) ? gettype($availabilityData['blocked_dates']) : 'not_set',
                'maintenance_dates_present' => isset($availabilityData['maintenance_dates']),
                'maintenance_dates_content' => $availabilityData['maintenance_dates'] ?? null,
                'unblocked_dates_present' => isset($availabilityData['unblocked_dates']),
                'unblocked_dates_content' => $availabilityData['unblocked_dates'] ?? null,
                'raw_availability_data' => $availabilityData
            ]);
        } else {
            \Log::warning('=== NO AVAILABILITY DATA IN REQUEST ===', [
                'property_id' => $id,
                'all_keys' => array_keys($request->all()),
                'request_size' => strlen(serialize($request->all()))
            ]);
        }

        $property = Property::query()->findOrFail($id);

        PropertyForm::createFromModel($property)
            ->setRequest($request)
            ->saving(function (PropertyForm $form) use ($propertyCategoryService, $saveFacilitiesService, $savePropertyCustomFieldService, $savePropertyAvailabilityService): void {
                $request = $form->getRequest();

                /**
                 * @var Property $property
                 */
                $property = $form->getModel();
                $property->fill($request->except(['expire_date']));
                $property->author_type = Account::class;
                $property->images = array_filter($request->input('images', []));
                $property->never_expired = $request->input('never_expired');
                $property->save();

                $form->fireModelEvents($property);

                if (RealEstateHelper::isEnabledCustomFields()) {
                    $savePropertyCustomFieldService->execute($property, $request->input('custom_fields', []));
                }

                $property->features()->sync($request->input('features', []));

                $saveFacilitiesService->execute($property, $request->input('facilities', []));

                // Enhanced logging before availability service call
                $availabilityData = $request->input('availability_data', []);
                \Log::info('=== BEFORE SavePropertyAvailabilityService CALL ===', [
                    'property_id' => $property->id,
                    'property_type' => $property->type,
                    'availability_data_present' => !empty($availabilityData),
                    'availability_data_keys' => array_keys($availabilityData),
                    'availability_data_content' => $availabilityData,
                    'request_method' => $request->method(),
                    'form_request_class' => get_class($request)
                ]);

                try {
                    $result = $savePropertyAvailabilityService->execute($property, $availabilityData);
                    \Log::info('=== SavePropertyAvailabilityService SUCCESS ===', [
                        'property_id' => $property->id,
                        'service_result' => $result,
                        'availability_data_processed' => $availabilityData
                    ]);
                } catch (\Exception $e) {
                    \Log::error('=== SavePropertyAvailabilityService ERROR ===', [
                        'property_id' => $property->id,
                        'error_message' => $e->getMessage(),
                        'error_trace' => $e->getTraceAsString(),
                        'availability_data' => $availabilityData
                    ]);
                    throw $e;
                }

                $propertyCategoryService->execute($request, $property);
            });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property.index'))
            ->setNextUrl(route('property.edit', $property->getKey()))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Property $property)
    {
        return DeleteResourceAction::make($property);
    }

    public function approve(Property $property)
    {
        abort_unless($property->is_pending_moderation, 404);

        $property->moderation_status = ModerationStatusEnum::APPROVED;
        $property->save();

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'author_name' => $property->author->name,
                'property_name' => $property->name,
                'property_link' => route('public.account.properties.edit', $property->getKey()),
            ])
            ->sendUsingTemplate('property-approved', $property->author->email);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property.index'))
            ->setMessage(trans('plugins/real-estate::property.status_moderation.approved'));
    }

    public function reject(Property $property, Request $request)
    {
        abort_unless($property->is_pending_moderation, 404);

        $request->validate([
            'reason' => ['required', 'string', 'max:400'],
        ]);

        $property->moderation_status = ModerationStatusEnum::REJECTED;
        $property->reject_reason = $request->input('reason');
        $property->save();

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'author_name' => $property->author->name,
                'property_name' => $property->name,
                'property_link' => route('public.account.properties.edit', $property->getKey()),
                'reason' => $request->input('reason'),
            ])
            ->sendUsingTemplate('property-rejected', $property->author->email);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property.index'))
            ->setMessage(trans('plugins/real-estate::property.status_moderation.rejected'));
    }
}
