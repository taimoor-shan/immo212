<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Forms\VacationRentalForm;
use Botble\RealEstate\Http\Requests\VacationRentalRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Services\SaveVacationRentalAvailabilityService;
use Botble\RealEstate\Services\SaveVacationRentalFacilitiesService;
use Botble\RealEstate\Services\StoreVacationRentalCategoryService;
use Botble\RealEstate\Tables\VacationRentalTable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VacationRentalController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::real-estate.name'), route('property.index'))
            ->add(trans('plugins/real-estate::vacation-rental.name'), route('vacation-rental.index'));
    }

    public function index(VacationRentalTable $table)
    {
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.create'));

        return VacationRentalForm::create()->renderForm();
    }

    public function store(
        VacationRentalRequest $request,
        StoreVacationRentalCategoryService $categoryService,
        SaveVacationRentalFacilitiesService $facilitiesService,
        SaveVacationRentalAvailabilityService $availabilityService
    ) {
        $request->merge([
            'images' => array_filter($request->input('images') ?: []),
            'author_type' => Account::class,
        ]);

        $vacationRentalForm = VacationRentalForm::create()->setRequest($request);

        $vacationRentalForm->saving(function (VacationRentalForm $form) use ($categoryService, $facilitiesService, $availabilityService): void {
            $request = $form->getRequest();

            /**
             * @var VacationRental $vacationRental
             */
            $vacationRental = $form->getModel();

            // Prepare data with proper defaults for nullable fields
            $authorId = $request->input('author_id');

            // Validate that author_id exists in re_accounts table if provided
            if ($authorId && !Account::find($authorId)) {
                $authorId = null;
            }

            // If no valid author_id and we're in admin context, set to null
            // (admin-created vacation rentals don't need a real estate account author)
            if (!$authorId && !auth('account')->check()) {
                $authorId = null;
            }

            $data = array_merge($request->input(), [
                'author_id' => $authorId,
                'author_type' => $request->input('author_type') ?: Account::class,
                'expire_date' => $request->input('never_expired') ? null : $request->input('expire_date'),
                'moderation_status' => $request->input('moderation_status') ?: ModerationStatusEnum::APPROVED,
                // Ensure numeric fields have proper defaults to prevent constraint violations
                'number_bedroom' => $request->input('number_bedroom') ?? 0,
                'number_bathroom' => $request->input('number_bathroom') ?? 0,
                'number_floor' => $request->input('number_floor') ?? 0,
            ]);

            $vacationRental->fill($data);
            $vacationRental->save();

            $form->fireModelEvents($vacationRental);

            if ($request->input('features')) {
                $vacationRental->features()->sync($request->input('features'));
            }

            $categoryService->execute($request, $vacationRental);

            $facilitiesService->execute($vacationRental, $request->input('facilities') ?: []);

            // Save availability data if provided
            if ($request->has('availability_data')) {
                $availabilityService->execute($vacationRental, $request->input('availability_data') ?: []);
            }
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('vacation-rental.index'))
            ->setNextUrl(route('vacation-rental.edit', $vacationRentalForm->getModel()->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function show(VacationRental $vacationRental)
    {
        $this->pageTitle($vacationRental->name);

        return view('plugins/real-estate::vacation-rental.show', compact('vacationRental'));
    }

    public function edit(VacationRental $vacationRental)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $vacationRental->name]));

        return VacationRentalForm::createFromModel($vacationRental)->renderForm();
    }

    public function update(
        VacationRental $vacationRental,
        VacationRentalRequest $request,
        StoreVacationRentalCategoryService $categoryService,
        SaveVacationRentalFacilitiesService $facilitiesService,
        SaveVacationRentalAvailabilityService $availabilityService
    ) {
        $request->merge([
            'images' => array_filter($request->input('images') ?: []),
            'author_type' => Account::class,
        ]);

        VacationRentalForm::createFromModel($vacationRental)
            ->setRequest($request)
            ->saving(function (VacationRentalForm $form) use ($categoryService, $facilitiesService, $availabilityService): void {
                $request = $form->getRequest();

                /**
                 * @var VacationRental $vacationRental
                 */
                $vacationRental = $form->getModel();

                // Prepare data with proper defaults for nullable fields
                $authorId = $request->input('author_id');

                // Validate that author_id exists in re_accounts table if provided
                if ($authorId && !Account::find($authorId)) {
                    $authorId = null;
                }

                $data = array_merge($request->except(['expire_date']), [
                    'author_id' => $authorId,
                    'author_type' => Account::class,
                    'images' => array_filter($request->input('images') ?: []),
                    'expire_date' => $request->input('never_expired') ? null : $request->input('expire_date'),
                    // Ensure numeric fields have proper defaults to prevent constraint violations
                    'number_bedroom' => $request->input('number_bedroom') ?? 0,
                    'number_bathroom' => $request->input('number_bathroom') ?? 0,
                    'number_floor' => $request->input('number_floor') ?? 0,
                ]);

                $vacationRental->fill($data);
                $vacationRental->save();

                $form->fireModelEvents($vacationRental);

                if ($request->input('features')) {
                    $vacationRental->features()->sync($request->input('features'));
                }

                $categoryService->execute($request, $vacationRental);

                $facilitiesService->execute($vacationRental, $request->input('facilities') ?: []);

                // Save availability data if provided
                if ($request->has('availability_data')) {
                    $availabilityService->execute($vacationRental, $request->input('availability_data') ?: []);
                }
            });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('vacation-rental.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(VacationRental $vacationRental)
    {
        return DeleteResourceAction::make($vacationRental);
    }

    public function approve(VacationRental $vacationRental)
    {
        abort_unless($vacationRental->is_pending_moderation, 404);

        $vacationRental->moderation_status = ModerationStatusEnum::APPROVED;
        $vacationRental->save();

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'author_name' => $vacationRental->author->name,
                'vacation_rental_name' => $vacationRental->name,
                'vacation_rental_link' => route('public.account.vacation-rentals.edit', $vacationRental->getKey()),
            ])
            ->sendUsingTemplate('vacation-rental-approved', $vacationRental->author->email);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('vacation-rental.index'))
            ->setMessage(trans('plugins/real-estate::vacation-rental.status_moderation.approved'));
    }

    public function reject(VacationRental $vacationRental, Request $request)
    {
        abort_unless($vacationRental->is_pending_moderation, 404);

        $request->validate([
            'reason' => ['required', 'string', 'max:400'],
        ]);

        $vacationRental->moderation_status = ModerationStatusEnum::REJECTED;
        $vacationRental->reject_reason = $request->input('reason');
        $vacationRental->save();

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'author_name' => $vacationRental->author->name,
                'vacation_rental_name' => $vacationRental->name,
                'vacation_rental_link' => route('public.account.vacation-rentals.edit', $vacationRental->getKey()),
                'reason' => $request->input('reason'),
            ])
            ->sendUsingTemplate('vacation-rental-rejected', $vacationRental->author->email);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('vacation-rental.index'))
            ->setMessage(trans('plugins/real-estate::vacation-rental.status_moderation.rejected'));
    }

    public function getAvailabilityData(Request $request)
    {
        $vacationRentalId = $request->input('vacation_rental_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$vacationRentalId || !$startDate || !$endDate) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $vacationRental = VacationRental::findOrFail($vacationRentalId);
        
        $availability = $vacationRental->availability()
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy('date');

        $events = $vacationRental->calendarEvents()
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get()
            ->map(fn($event) => $event->toFullCalendarEvent());

        return response()->json([
            'availability' => $availability,
            'events' => $events,
        ]);
    }

    public function blockDates(Request $request)
    {
        $request->validate([
            'vacation_rental_id' => 'required|exists:re_vacation_rentals,id',
            'dates' => 'required|array',
            'dates.*' => 'required|date',
            'reason' => 'nullable|string|max:255',
        ]);

        $vacationRental = VacationRental::findOrFail($request->input('vacation_rental_id'));

        foreach ($request->input('dates') as $date) {
            $vacationRental->availability()->updateOrCreate([
                'date' => $date,
            ], [
                'status' => 'blocked',
                'notes' => $request->input('reason'),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function unblockDates(Request $request)
    {
        $request->validate([
            'vacation_rental_id' => 'required|exists:re_vacation_rentals,id',
            'dates' => 'required|array',
            'dates.*' => 'required|date',
        ]);

        $vacationRental = VacationRental::findOrFail($request->input('vacation_rental_id'));

        foreach ($request->input('dates') as $date) {
            $vacationRental->availability()->updateOrCreate([
                'date' => $date,
            ], [
                'status' => 'available',
                'notes' => null,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function maintenanceDates(Request $request)
    {
        $request->validate([
            'vacation_rental_id' => 'required|exists:re_vacation_rentals,id',
            'dates' => 'required|array',
            'dates.*' => 'required|date',
            'reason' => 'nullable|string|max:255',
        ]);

        $vacationRental = VacationRental::findOrFail($request->input('vacation_rental_id'));

        foreach ($request->input('dates') as $date) {
            $vacationRental->availability()->updateOrCreate([
                'date' => $date,
            ], [
                'status' => 'maintenance',
                'notes' => $request->input('reason'),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
