<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Services\SaveVacationRentalAvailabilityService;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Botble\Base\Facades\Assets;

class VacationRentalController extends BaseController
{
    protected SaveVacationRentalAvailabilityService $availabilityService;

    public function __construct(SaveVacationRentalAvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    protected function getViewFileName(string $view): string
    {
        if (view()->exists($themeView = Theme::getThemeNamespace('views.real-estate.' . $view))) {
            return $themeView;
        }

        return 'plugins/real-estate::themes.' . $view;
    }

    public function dashboard()
    {
        $user = auth('account')->user();

        // Get vacation rental properties for this user
        $vacationRentals = $user->vacationRentals()
            ->with(['availability', 'bookings'])
            ->get();

        // Calculate summary statistics
        $totalProperties = $vacationRentals->count();
        $totalBookings = VacationRentalBooking::whereIn('vacation_rental_id', $vacationRentals->pluck('id'))
            ->where('status', '!=', VacationRentalBooking::STATUS_CANCELLED)
            ->count();

        $currentMonth = Carbon::now();
        $monthlyRevenue = VacationRentalBooking::whereIn('vacation_rental_id', $vacationRentals->pluck('id'))
            ->where('status', VacationRentalBooking::STATUS_CONFIRMED)
            ->where('payment_status', VacationRentalBooking::PAYMENT_PAID)
            ->whereMonth('check_in_date', $currentMonth->month)
            ->whereYear('check_in_date', $currentMonth->year)
            ->sum('total_amount');

        // Get recent bookings
        $recentBookings = VacationRentalBooking::whereIn('vacation_rental_id', $vacationRentals->pluck('id'))
            ->with('vacationRental')
            ->latest()
            ->limit(5)
            ->get();

        // Get upcoming check-ins
        $upcomingCheckIns = VacationRentalBooking::whereIn('vacation_rental_id', $vacationRentals->pluck('id'))
            ->where('status', VacationRentalBooking::STATUS_CONFIRMED)
            ->where('check_in_date', '>=', Carbon::today())
            ->where('check_in_date', '<=', Carbon::today()->addDays(7))
            ->with('vacationRental')
            ->orderBy('check_in_date')
            ->get();

        $this->pageTitle(__('Vacation Rental Dashboard'));

        Assets::usingVueJS()
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        return view($this->getViewFileName('dashboard.vacation-rentals.index'), compact(
            'user',
            'vacationRentals',
            'totalProperties',
            'totalBookings',
            'monthlyRevenue',
            'recentBookings',
            'upcomingCheckIns'
        ));
    }

    public function bookings(Request $request)
    {
        $user = auth('account')->user();
        $vacationRentalIds = $user->vacationRentals()->pluck('id');

        $query = VacationRentalBooking::whereIn('vacation_rental_id', $vacationRentalIds)
            ->with(['vacationRental']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('check_in_date', '>=', Carbon::parse($request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->where('check_out_date', '<=', Carbon::parse($request->date_to));
        }

        $bookings = $query->latest()->paginate(20);

        $properties = $user->vacationRentals()
            ->select('id', 'name')
            ->get();

        $this->pageTitle(__('Vacation Rental Bookings'));

        return view($this->getViewFileName('dashboard.vacation-rentals.bookings'), compact(
            'bookings',
            'properties'
        ));
    }

    public function availability(Request $request)
    {
        $user = auth('account')->user();
        $properties = $user->vacationRentals()->get();

        $selectedProperty = null;
        $availabilityData = [];
        $calendarEvents = [];

        if ($request->filled('property_id')) {
            $selectedProperty = $properties->where('id', $request->property_id)->first();

            if ($selectedProperty) {
                $startDate = Carbon::parse($request->get('month', Carbon::now()->format('Y-m')))->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();

                $availabilityData = $this->availabilityService->getAvailabilityDetails(
                    $selectedProperty->id,
                    $startDate,
                    $endDate
                );

                $calendarEvents = $this->availabilityService->getCalendarEvents(
                    $selectedProperty->id,
                    $startDate,
                    $endDate
                );
            }
        }

        $this->pageTitle(__('Availability Management'));

        Assets::usingVueJS()
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/components.js');

        return view($this->getViewFileName('dashboard.vacation-rentals.availability'), compact(
            'properties',
            'selectedProperty',
            'availabilityData',
            'calendarEvents'
        ));
    }

    public function calendar(Request $request)
    {
        $user = auth('account')->user();
        $properties = $user->vacationRentals()->get();

        $selectedProperty = null;
        $monthlyData = [];

        if ($request->filled('property_id')) {
            $selectedProperty = $properties->where('id', $request->property_id)->first();

            if ($selectedProperty) {
                $year = $request->get('year', Carbon::now()->year);
                $month = $request->get('month', Carbon::now()->month);

                $monthlyData = $this->availabilityService->getMonthlyAvailabilitySummary(
                    $selectedProperty->id,
                    $year,
                    $month
                );
            }
        }

        $this->pageTitle(__('Availability Calendar'));

        // Add necessary assets for the calendar
        Assets::addStylesDirectly([
            'vendor/core/plugins/real-estate/css/calendar-backend.css',
            'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
        ])->addScriptsDirectly([
            'https://cdn.jsdelivr.net/npm/flatpickr',
            'vendor/core/plugins/real-estate/js/vacation-rental-form.js',
        ]);

        return view($this->getViewFileName('dashboard.vacation-rentals.calendar-new'), compact(
            'properties',
            'selectedProperty',
            'monthlyData'
        ));
    }

    public function blockDates(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:re_vacation_rentals,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = auth('account')->user();
        $vacationRental = $user->vacationRentals()
            ->where('id', $request->property_id)
            ->firstOrFail();

        $this->availabilityService->blockDates(
            $vacationRental,
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date),
            $request->reason ?? 'Blocked by owner'
        );

        return $this->httpResponse()
            ->setMessage(__('Dates blocked successfully'));
    }

    public function unblockDates(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:re_vacation_rentals,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = auth('account')->user();
        $vacationRental = $user->vacationRentals()
            ->where('id', $request->property_id)
            ->firstOrFail();

        $this->availabilityService->unblockDates(
            $vacationRental,
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return $this->httpResponse()
            ->setMessage(__('Dates unblocked successfully'));
    }

    public function maintenanceDates(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:re_properties,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = auth('account')->user();
        $property = $user->vacationRentals()
            ->where('id', $request->property_id)
            ->firstOrFail();

        $this->availabilityService->maintenanceDates(
            $property->id,
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date),
            $request->reason ?? 'Maintenance'
        );

        return $this->httpResponse()
            ->setMessage(__('Dates set to maintenance successfully'));
    }

    public function updateBookingStatus(Request $request, $bookingId)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(VacationRentalBooking::getStatuses())),
        ]);

        $user = auth('account')->user();
        $vacationRentalIds = $user->vacationRentals()->pluck('id');

        $booking = VacationRentalBooking::whereIn('vacation_rental_id', $vacationRentalIds)
            ->findOrFail($bookingId);

        $booking->update(['status' => $request->status]);

        return $this->httpResponse()
            ->setMessage(__('Booking status updated successfully'));
    }

    /**
     * Get availability data for property edit calendar (AJAX endpoint)
     */
    public function getAvailabilityDataForEdit(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:re_vacation_rentals,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = auth('account')->user();
        $vacationRental = $user->vacationRentals()
            ->where('id', $request->property_id)
            ->firstOrFail();

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $availability = $this->availabilityService->getAvailabilityDetails(
            $vacationRental,
            $startDate,
            $endDate
        );

        return $this->httpResponse()->setData($availability);
    }

    /**
     * Get availability data for frontend calendar (AJAX endpoint)
     */
    public function getAvailabilityData(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:re_properties,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $property = VacationRental::findOrFail($request->property_id);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $availabilityData = $this->availabilityService->getAvailabilityDetails(
            $property->id,
            $startDate,
            $endDate
        );

        return $this->httpResponse()->setData($availabilityData);
    }

    /**
     * Calculate booking price for frontend (AJAX endpoint)
     */
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:re_vacation_rentals,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        $property = VacationRental::findOrFail($request->property_id);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $guests = $request->integer('guests');

        // Check availability
        if (!$this->availabilityService->checkAvailability($property->id, $checkIn, $checkOut)) {
            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Selected dates are not available'));
        }

        // Validate minimum stay
        if (!$this->availabilityService->validateMinimumStay($property->id, $checkIn, $checkOut)) {
            $effectiveMinimumStay = $this->availabilityService->getEffectiveMinimumStay($property->id, $checkIn);
            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Minimum stay is :nights nights', ['nights' => $effectiveMinimumStay]));
        }

        // Check maximum guests
        if ($property->maximum_guests && $guests > $property->maximum_guests) {
            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Maximum :guests guests allowed', ['guests' => $property->maximum_guests]));
        }

        try {
            $pricing = $this->availabilityService->calculateBookingPrice($property->id, $checkIn, $checkOut, $guests);

            return $this->httpResponse()->setData([
                'pricing' => $pricing,
                'property' => [
                    'name' => $property->name,
                    'check_in_time' => $property->check_in_time,
                    'check_out_time' => $property->check_out_time,
                    'house_rules' => $property->house_rules,
                    'cancellation_policy' => $property->cancellation_policy,
                ]
            ]);
        } catch (\Exception $e) {
            return $this->httpResponse()
                ->setError()
                ->setMessage($e->getMessage());
        }
    }
}
