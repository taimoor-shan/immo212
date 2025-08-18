<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Models\VacationRentalAvailability;
use Botble\RealEstate\Models\VacationRentalCalendarEvent;
use Botble\RealEstate\Services\SaveVacationRentalAvailabilityService;
use Botble\RealEstate\Tables\VacationRentalBookingTable;
use Botble\RealEstate\Tables\VacationRentalTable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class VacationRentalAdminController extends BaseController
{
    public function __construct(protected SaveVacationRentalAvailabilityService $availabilityService)
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::real-estate.name'));
    }

    protected function setupBreadcrumb(): void
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::vacation-rental.name'), route('vacation-rental.index'));
    }

    public function index(VacationRentalTable $dataTable)
    {
        $this->setupBreadcrumb();
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.name'));

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-admin.css');

        return $dataTable->renderTable();
    }

    public function overview()
    {
        $this->setupBreadcrumb();
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.overview'));

        // Get vacation rental statistics
        $totalProperties = VacationRental::count();
        $totalBookings = VacationRentalBooking::count();
        $activeBookings = VacationRentalBooking::where('status', VacationRentalBooking::STATUS_CONFIRMED)
            ->where('check_in_date', '<=', Carbon::today())
            ->where('check_out_date', '>', Carbon::today())
            ->count();

        // Monthly revenue
        $monthlyRevenue = VacationRentalBooking::where('status', VacationRentalBooking::STATUS_CONFIRMED)
            ->whereMonth('check_in_date', Carbon::now()->month)
            ->whereYear('check_in_date', Carbon::now()->year)
            ->sum('total_amount');

        // Recent bookings
        $recentBookings = VacationRentalBooking::with(['vacationRental'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming check-ins
        $upcomingCheckIns = VacationRentalBooking::with(['vacationRental'])
            ->where('status', VacationRentalBooking::STATUS_CONFIRMED)
            ->where('check_in_date', '>=', Carbon::today())
            ->where('check_in_date', '<=', Carbon::today()->addDays(7))
            ->orderBy('check_in_date')
            ->get();

        // Properties needing attention
        $propertiesNeedingAttention = VacationRental::whereDoesntHave('bookings', function ($query) {
                $query->where('check_in_date', '>=', Carbon::now()->subDays(30));
            })
            ->limit(5)
            ->get();

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-admin.css');

        return view('plugins/real-estate::vacation-rental.overview', compact(
            'totalProperties',
            'totalBookings',
            'activeBookings',
            'monthlyRevenue',
            'recentBookings',
            'upcomingCheckIns',
            'propertiesNeedingAttention'
        ));
    }

    public function properties(VacationRentalPropertyTable $dataTable, Request $request)
    {
        // Handle DataTable AJAX requests
        if ($request->ajax()) {
            return $dataTable->renderTable();
        }

        $this->setupBreadcrumb();
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.properties'));

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-admin.css');

        return $dataTable->render('plugins/real-estate::vacation-rental.properties');
    }



    public function dashboard()
    {
        $this->setupBreadcrumb();
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.dashboard'));

        // Get vacation rental statistics
        $totalProperties = VacationRental::count();
        $totalBookings = VacationRentalBooking::count();
        $activeBookings = VacationRentalBooking::where('status', VacationRentalBooking::STATUS_CONFIRMED)
            ->where('check_in_date', '<=', Carbon::today())
            ->where('check_out_date', '>', Carbon::today())
            ->count();

        // Monthly revenue
        $monthlyRevenue = VacationRentalBooking::where('status', VacationRentalBooking::STATUS_CONFIRMED)
            ->whereMonth('check_in_date', Carbon::now()->month)
            ->whereYear('check_in_date', Carbon::now()->year)
            ->sum('total_amount');

        // Recent bookings
        $recentBookings = VacationRentalBooking::with(['property'])
            ->latest()
            ->limit(10)
            ->get();

        // Upcoming check-ins
        $upcomingCheckIns = VacationRentalBooking::with(['property'])
            ->where('status', VacationRentalBooking::STATUS_CONFIRMED)
            ->where('check_in_date', '>=', Carbon::today())
            ->where('check_in_date', '<=', Carbon::today()->addDays(7))
            ->orderBy('check_in_date')
            ->get();

        // Properties needing attention
        $propertiesNeedingAttention = VacationRental::where(function ($query) {
                $query->where('moderation_status', 'pending')
                    ->orWhereNull('check_in_time')
                    ->orWhereNull('check_out_time')
                    ->orWhereNull('minimum_stay');
            })
            ->limit(10)
            ->get();

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-admin.css');

        return view('plugins/real-estate::vacation-rental.dashboard', compact(
            'totalProperties',
            'totalBookings',
            'activeBookings',
            'monthlyRevenue',
            'recentBookings',
            'upcomingCheckIns',
            'propertiesNeedingAttention'
        ));
    }

    public function bookings(VacationRentalBookingTable $dataTable)
    {
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.bookings'));

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-admin.css');

        return $dataTable->renderTable();
    }

    public function availability(Request $request)
    {
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.availability'));

        $properties = VacationRental::select('id', 'name')
            ->orderBy('name')
            ->get();

        $selectedProperty = null;
        $availabilityData = [];
        $calendarEvents = [];

        if ($request->filled('property_id')) {
            $selectedProperty = VacationRental::find($request->property_id);

            if ($selectedProperty) {
                $startDate = Carbon::parse($request->get('month', Carbon::now()->format('Y-m')))->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();

                $availabilityData = $this->availabilityService->getAvailabilityDetails(
                    $selectedProperty,
                    $startDate,
                    $endDate
                );

                // Get calendar events directly from the vacation rental
                $calendarEvents = $selectedProperty->calendarEvents()
                    ->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->get()
                    ->map(fn($event) => $event->toFullCalendarEvent());
            }
        }

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-admin.css')
            ->addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-calendar-admin.css')
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/admin-calendar.js');

        return view('plugins/real-estate::vacation-rental.availability', compact(
            'properties',
            'selectedProperty',
            'availabilityData',
            'calendarEvents'
        ));
    }

    public function calendar(Request $request)
    {
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.calendar'));

        $properties = VacationRental::select('id', 'name')
            ->orderBy('name')
            ->get();

        $selectedProperty = null;
        $monthlyData = [];

        if ($request->filled('property_id')) {
            $selectedProperty = VacationRental::find($request->property_id);

            if ($selectedProperty) {
                $year = $request->get('year', Carbon::now()->year);
                $month = $request->get('month', Carbon::now()->month);

                $monthlyData = $this->availabilityService->getMonthlyAvailabilitySummary(
                    $selectedProperty,
                    $year,
                    $month
                );
            }
        }

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-admin.css')
            ->addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-calendar-admin.css')
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/admin-calendar.js');

        return view('plugins/real-estate::vacation-rental.calendar', compact(
            'properties',
            'selectedProperty',
            'monthlyData'
        ));
    }

    public function blockDates(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:re_vacation_rentals,id',
            'dates' => 'required|array',
            'dates.*' => 'date',
            'reason' => 'nullable|string|max:255',
        ]);

        $vacationRental = VacationRental::findOrFail($request->property_id);

        $this->availabilityService->blockDates(
            $vacationRental,
            $request->dates,
            $request->reason ?? 'Blocked by admin'
        );

        return $this->httpResponse()
            ->setMessage(__('Dates blocked successfully'));
    }

    public function unblockDates(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:re_vacation_rentals,id',
            'dates' => 'required|array',
            'dates.*' => 'date',
        ]);

        $vacationRental = VacationRental::findOrFail($request->property_id);

        $this->availabilityService->unblockDates(
            $vacationRental,
            $request->dates
        );

        return $this->httpResponse()
            ->setMessage(__('Dates unblocked successfully'));
    }

    public function maintenanceDates(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:re_vacation_rentals,id',
            'dates' => 'required|array',
            'dates.*' => 'date',
            'reason' => 'nullable|string|max:255',
        ]);

        $vacationRental = VacationRental::findOrFail($request->property_id);

        $this->availabilityService->maintenanceDates(
            $vacationRental,
            $request->dates,
            $request->reason ?? 'Maintenance'
        );

        return $this->httpResponse()
            ->setMessage(__('Dates set to maintenance successfully'));
    }

    public function getAvailabilityData(Request $request)
    {
        // Add debugging
        \Log::info('VacationRentalAdminController::getAvailabilityData called', [
            'property_id' => $request->property_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => auth()->id(),
            'is_authenticated' => auth()->check(),
            'request_headers' => $request->headers->all(),
        ]);

        $request->validate([
            'property_id' => 'required|exists:re_vacation_rentals,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $vacationRental = VacationRental::findOrFail($request->property_id);

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            $availabilityData = $this->availabilityService->getAvailabilityExceptions(
                $vacationRental,
                $startDate,
                $endDate
            );

            \Log::info('Availability data loaded successfully', [
                'property_id' => $request->property_id,
                'data_count' => count($availabilityData),
            ]);

            return $this->httpResponse()->setData($availabilityData);

        } catch (\Exception $e) {
            \Log::error('Failed to load availability data', [
                'property_id' => $request->property_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Failed to load availability data.'));
        }
    }

    public function showBooking($id)
    {
        $booking = VacationRentalBooking::with(['vacationRental', 'vacationRental.currency'])
            ->findOrFail($id);

        $this->setupBreadcrumb();
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.booking_details'));

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-admin.css');

        return view('plugins/real-estate::vacation-rental.booking-show', compact('booking'));
    }

    public function editBooking($id)
    {
        $booking = VacationRentalBooking::with(['vacationRental', 'vacationRental.currency'])
            ->findOrFail($id);

        $this->setupBreadcrumb();
        $this->pageTitle(trans('plugins/real-estate::vacation-rental.edit_booking'));

        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/vacation-rental-admin.css');

        return view('plugins/real-estate::vacation-rental.booking-edit', compact('booking'));
    }

    public function updateBooking(Request $request, $id): RedirectResponse
    {
        $booking = VacationRentalBooking::findOrFail($id);

        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(VacationRentalBooking::getStatuses())),
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $booking->update([
            'status' => $request->status,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'special_requests' => $request->special_requests,
        ]);

        return redirect()
            ->route('vacation-rental.admin.bookings')
            ->with('success_msg', trans('plugins/real-estate::vacation-rental.booking_updated_successfully'));
    }

    public function destroyBooking($id)
    {
        try {
            $booking = VacationRentalBooking::findOrFail($id);

            // Note: Availability cleanup is handled automatically by the VacationRentalBooking model's deleted event
            $booking->delete();

            return $this->httpResponse()
                ->setMessage(trans('plugins/real-estate::vacation-rental.booking_deleted_successfully'));
        } catch (Exception $exception) {
            return $this->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }


}
