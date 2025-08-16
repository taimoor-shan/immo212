<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\VacationRentalForm;
use Botble\RealEstate\Http\Requests\VacationRentalRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\CustomFieldValue;
use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Models\VacationRentalAvailability;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Tables\VacationRentalTable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class VacationRentalController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::vacation-rental.name'), route('vacation-rental.index'));
    }

    public function index(VacationRentalTable $dataTable)
    {
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.name'));

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.create'));

        return VacationRentalForm::create()->renderForm();
    }

    public function store(
        VacationRentalRequest $request,
        SaveFacilitiesService $saveFacilitiesService
    ) {
        $request->merge([
            'images' => array_filter($request->input('images', [])),
            'author_type' => Account::class,
        ]);

        $vacationRentalForm = VacationRentalForm::create()->setRequest($request);

        $vacationRentalForm->saving(function (VacationRentalForm $form) use ($saveFacilitiesService): void {
            $request = $form->getRequest();

            /**
             * @var VacationRental $vacationRental
             */
            $vacationRental = $form->getModel();
            $vacationRental->fill($request->input());
            $vacationRental->save();

            $form->fireModelEvents($vacationRental);

            if (RealEstateHelper::isEnabledCustomFields()) {
                $this->saveCustomFields($vacationRental, $request->input('custom_fields', []));
            }

            $vacationRental->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($vacationRental, $request->input('facilities', []));

            if ($request->has('categories')) {
                $vacationRental->categories()->sync($request->input('categories', []));
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
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.edit', ['name' => $vacationRental->name]));

        return view('plugins/real-estate::vacation-rental.show', compact('vacationRental'));
    }

    public function edit(int|string $id)
    {
        /**
         * @var VacationRental $vacationRental
         */
        $vacationRental = VacationRental::query()->findOrFail($id);

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $vacationRental->name]));

        return VacationRentalForm::createFromModel($vacationRental)->renderForm();
    }

    public function update(
        int|string $id,
        VacationRentalRequest $request,
        SaveFacilitiesService $saveFacilitiesService
    ) {
        $vacationRental = VacationRental::query()->findOrFail($id);

        $request->merge([
            'images' => array_filter($request->input('images', [])),
            'author_type' => Account::class,
        ]);

        $vacationRentalForm = VacationRentalForm::createFromModel($vacationRental)->setRequest($request);

        $vacationRentalForm->saving(function (VacationRentalForm $form) use ($saveFacilitiesService): void {
            $request = $form->getRequest();

            /**
             * @var VacationRental $vacationRental
             */
            $vacationRental = $form->getModel();
            $vacationRental->fill($request->input());
            $vacationRental->save();

            if (RealEstateHelper::isEnabledCustomFields()) {
                $this->saveCustomFields($vacationRental, $request->input('custom_fields', []));
            }

            $vacationRental->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($vacationRental, $request->input('facilities', []));

            if ($request->has('categories')) {
                $vacationRental->categories()->sync($request->input('categories', []));
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

        if ($vacationRental->author && $vacationRental->author->email) {
            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'author_name' => $vacationRental->author->name,
                    'post_name' => $vacationRental->name,
                    'post_url' => route('public.account.vacation-rentals.edit', $vacationRental->getKey()),
                ])
                ->sendUsingTemplate('property-approved', $vacationRental->author->email);
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('vacation-rental.index'))
            ->setMessage(trans('plugins/real-estate::property.status_moderation.approved'));
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

        if ($vacationRental->author && $vacationRental->author->email) {
            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'author_name' => $vacationRental->author->name,
                    'post_name' => $vacationRental->name,
                    'post_url' => route('public.account.vacation-rentals.edit', $vacationRental->getKey()),
                    'reason' => $request->input('reason'),
                ])
                ->sendUsingTemplate('property-rejected', $vacationRental->author->email);
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('vacation-rental.index'))
            ->setMessage(trans('plugins/real-estate::property.status_moderation.rejected'));
    }

    protected function saveCustomFields(VacationRental $vacationRental, array $customFields = []): void
    {
        $customFields = CustomFieldValue::formatCustomFields($customFields);

        $vacationRental->customFields()
            ->whereNotIn('id', collect($customFields)->pluck('id')->all())
            ->delete();

        $vacationRental->customFields()->saveMany($customFields);
    }

    // Calendar and availability management
    public function calendar()
    {
        $this->pageTitle('Vacation Rental Calendar');

        $vacationRentals = VacationRental::query()
            ->published()
            ->get();

        return view('plugins/real-estate::vacation-rental.calendar', compact('vacationRentals'));
    }

    public function availability(Request $request)
    {
        $this->pageTitle('Vacation Rental Availability');

        $vacationRentals = VacationRental::query()
            ->published()
            ->get();

        $selectedVacationRental = null;
        $availabilityData = [];

        if ($request->has('vacation_rental_id')) {
            $selectedVacationRental = VacationRental::find($request->get('vacation_rental_id'));
            
            if ($selectedVacationRental) {
                $month = $request->get('month', Carbon::now()->format('Y-m'));
                $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                
                $availabilityData = VacationRentalAvailability::getAvailabilityForDateRange(
                    $selectedVacationRental->id,
                    $startDate,
                    $endDate
                );
            }
        }

        return view('plugins/real-estate::vacation-rental.availability', compact(
            'vacationRentals',
            'selectedVacationRental',
            'availabilityData'
        ));
    }

    // API endpoints for calendar management
    public function getAvailabilityData(Request $request, BaseHttpResponse $response)
    {
        $vacationRentalId = $request->get('vacation_rental_id');
        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));

        if (!$vacationRentalId) {
            return $response->setError()->setMessage('Vacation rental ID is required');
        }

        $availabilityData = VacationRentalAvailability::getAvailabilityForDateRange(
            $vacationRentalId,
            $startDate,
            $endDate
        );

        return $response->setData($availabilityData);
    }

    public function blockDates(Request $request, BaseHttpResponse $response)
    {
        $request->validate([
            'vacation_rental_id' => 'required|exists:re_vacation_rentals,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        try {
            VacationRentalAvailability::blockDates(
                $request->get('vacation_rental_id'),
                Carbon::parse($request->get('start_date')),
                Carbon::parse($request->get('end_date')),
                $request->get('reason')
            );

            return $response->setMessage('Dates blocked successfully');
        } catch (Exception $e) {
            return $response->setError()->setMessage($e->getMessage());
        }
    }

    public function unblockDates(Request $request, BaseHttpResponse $response)
    {
        $request->validate([
            'vacation_rental_id' => 'required|exists:re_vacation_rentals,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            VacationRentalAvailability::unblockDates(
                $request->get('vacation_rental_id'),
                Carbon::parse($request->get('start_date')),
                Carbon::parse($request->get('end_date'))
            );

            return $response->setMessage('Dates unblocked successfully');
        } catch (Exception $e) {
            return $response->setError()->setMessage($e->getMessage());
        }
    }

    public function maintenanceDates(Request $request, BaseHttpResponse $response)
    {
        $request->validate([
            'vacation_rental_id' => 'required|exists:re_vacation_rentals,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        try {
            VacationRentalAvailability::setMaintenanceDates(
                $request->get('vacation_rental_id'),
                Carbon::parse($request->get('start_date')),
                Carbon::parse($request->get('end_date')),
                $request->get('reason')
            );

            return $response->setMessage('Maintenance dates set successfully');
        } catch (Exception $e) {
            return $response->setError()->setMessage($e->getMessage());
        }
    }

    // Booking management
    public function bookings()
    {
        $this->pageTitle('Vacation Rental Bookings');

        $bookings = VacationRentalBooking::with(['vacationRental', 'account'])
            ->whereNotNull('vacation_rental_id')
            ->latest()
            ->paginate(20);

        return view('plugins/real-estate::vacation-rental.bookings', compact('bookings'));
    }

    public function showBooking(VacationRentalBooking $booking)
    {
        if (!$booking->vacation_rental_id) {
            abort(404);
        }

        $this->pageTitle('Booking #' . $booking->booking_number);

        return view('plugins/real-estate::vacation-rental.booking-details', compact('booking'));
    }

    public function updateBookingStatus(VacationRentalBooking $booking, Request $request, BaseHttpResponse $response)
    {
        if (!$booking->vacation_rental_id) {
            return $response->setError()->setMessage('Invalid booking');
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(VacationRentalBooking::getStatuses())),
        ]);

        try {
            $booking->update(['status' => $request->get('status')]);

            return $response->setMessage('Booking status updated successfully');
        } catch (Exception $e) {
            return $response->setError()->setMessage($e->getMessage());
        }
    }
}
