<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Services\SaveVacationRentalAvailabilityService;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\VacationRental;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Http\Requests\VacationRentalBookingRequest;
use Botble\RealEstate\Http\Requests\VacationRentalInquiryRequest;
use Botble\RealEstate\Notifications\VacationRentalBookingConfirmationNotification;
use Botble\RealEstate\Notifications\VacationRentalBookingOwnerNotification;
use Botble\RealEstate\Notifications\VacationRentalBookingProcessingNotification;
use Botble\Base\Facades\EmailHandler;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

class VacationRentalBookingController extends BaseController
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

    public function showBookingForm(Request $request, $vacationRentalSlug)
    {
        $slug = SlugHelper::getSlug($vacationRentalSlug, SlugHelper::getPrefix(VacationRental::class));

        if (!$slug) {
            abort(404);
        }

        $vacationRental = VacationRental::where('id', $slug->reference_id)
            ->where('moderation_status', 'approved')
            ->firstOrFail();

        // Validate query parameters
        $checkIn = $request->get('check_in');
        $checkOut = $request->get('check_out');
        $guests = $request->get('guests', 1);

        if (!$checkIn || !$checkOut) {
            return redirect($vacationRental->url)->with('error', __('Please select check-in and check-out dates.'));
        }

        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);

        // Validate dates
        if ($checkInDate->isPast() || $checkOutDate->isPast() || $checkOutDate <= $checkInDate) {
            return redirect($vacationRental->url)->with('error', __('Invalid dates selected.'));
        }

        // Check availability
        if (!$this->availabilityService->checkAvailability($vacationRental, $checkInDate, $checkOutDate)) {
            return redirect($vacationRental->url)->with('error', __('Selected dates are not available.'));
        }

        // Validate minimum stay (simplified for now)
        $nights = $checkInDate->diffInDays($checkOutDate);
        $minimumStay = $vacationRental->minimum_stay ?? 1;
        if ($nights < $minimumStay) {
            return redirect($vacationRental->url)->with('error', __('Minimum stay is :nights nights.', ['nights' => $minimumStay]));
        }

        // Check maximum guests
        if ($vacationRental->maximum_guests && $guests > $vacationRental->maximum_guests) {
            return redirect($vacationRental->url)->with('error', __('Maximum :guests guests allowed.', ['guests' => $vacationRental->maximum_guests]));
        }

        try {
            $pricing = $this->availabilityService->calculateBookingPrice($vacationRental, $checkInDate, $checkOutDate, $guests);
        } catch (\Exception $e) {
            return redirect($vacationRental->url)->with('error', $e->getMessage());
        }

        $this->pageTitle(__('Book :property', ['property' => $property->name]));

        return Theme::scope('real-estate.booking.form', compact(
            'property',
            'checkInDate',
            'checkOutDate',
            'guests',
            'pricing'
        ))->render();
    }

    public function processBooking(VacationRentalBookingRequest $request)
    {
        // Debug logging
        Log::info('Vacation rental booking process started', [
            'request_data' => $request->all(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);

        Log::info('Booking validation passed, processing dates', [
            'check_in_date_raw' => $request['check_in_date'],
            'check_out_date_raw' => $request['check_out_date'],
        ]);

        $vacationRentalId = $request['vacation_rental_id'];

        Log::info('Looking for vacation rental', [
            'vacation_rental_id' => $vacationRentalId,
            'vacation_rental_id_type' => gettype($vacationRentalId)
        ]);

        $property = VacationRental::where('id', $vacationRentalId)
            ->where('moderation_status', 'approved')
            ->first();

        if (!$property) {
            Log::error('Vacation rental not found or not approved', [
                'vacation_rental_id' => $vacationRentalId,
                'all_vacation_rentals' => VacationRental::pluck('id', 'name')->toArray()
            ]);

            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Vacation rental not found or not available for booking.'))
                ->setCode(404);
        }

        $checkInDate = Carbon::parse($request['check_in_date']);
        $checkOutDate = Carbon::parse($request['check_out_date']);



        // Re-validate availability (in case it changed)
        if (!$this->availabilityService->checkAvailability($property, $checkInDate, $checkOutDate)) {
            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Selected dates are no longer available.'));
        }

        try {
            $pricing = $this->availabilityService->calculateBookingPrice(
                $property,
                $checkInDate,
                $checkOutDate,
                $request['guests_count']
            );

            DB::beginTransaction();

            // Create booking
            $booking = VacationRentalBooking::create([
                'booking_number' => $this->generateBookingNumber(),
                'vacation_rental_id' => $property->id,
                'guest_name' => $request['guest_name'],
                'guest_email' => $request['guest_email'],
                'guest_phone' => $request['guest_phone'],
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'nights_count' => $pricing['nights'],
                'guests_count' => $request['guests_count'],

                'base_price_per_night' => $pricing['base_price_per_night'],
                'total_nights_cost' => $pricing['total_nights_cost'],
                'cleaning_fee' => $pricing['cleaning_fee'],
                'service_fee' => $pricing['service_fee'],
                'taxes' => $pricing['taxes'],
                'security_deposit' => $pricing['security_deposit'],
                'total_amount' => $pricing['total_amount'],
                'special_requests' => $request['special_requests'],
                'status' => VacationRentalBooking::STATUS_PENDING,
                'payment_status' => VacationRentalBooking::PAYMENT_PENDING,
            ]);

            // Send booking processing email to customer
            try {
                Notification::route('mail', $booking->guest_email)
                    ->notify(new VacationRentalBookingProcessingNotification($booking));
            } catch (\Exception $e) {
                Log::error('Failed to send booking processing email: ' . $e->getMessage(), [
                    'booking_id' => $booking->id,
                    'customer_email' => $booking->guest_email,
                ]);
            }

            // Note: Dates are automatically set to STATUS_BOOKED by the VacationRentalBooking model's created event
            // No need to manually block dates as they are already marked as booked

            DB::commit();

            // Redirect to payment
            return $this->redirectToPayment($booking, $request);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return $this->httpResponse()
                    ->setError()
                    ->setMessage(__('Booking failed: :error', ['error' => $e->getMessage()]));
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Booking failed: :error', ['error' => $e->getMessage()]));
        }
    }

    protected function redirectToPayment(VacationRentalBooking $booking, Request $request)
    {
        Log::info('Redirecting to payment', [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'payment_method' => $request['payment_method'],
            'total_amount' => $booking->total_amount
        ]);

        $paymentData = [
            'amount' => $booking->total_amount,
            'currency' => config('plugins.payment.payment.currency', 'USD'),
            'order_id' => [$booking->id],
            'customer_id' => null, // Guest booking
            'customer_type' => null,
            'return_url' => route('public.vacation-rental.booking.success', $booking->booking_number),
            'callback_url' => route('public.vacation-rental.booking.callback'),
            'payment_method' => $request['payment_method'],
            'description' => __('Vacation Rental Booking #:number', ['number' => $booking->booking_number]),
        ];

        // Store booking data in session for payment callback
        session([
            'vacation_rental_booking_id' => $booking->id,
            'vacation_rental_payment_data' => $paymentData,
        ]);

        try {
            // For JSON requests with test payment method, return success directly
            if ($request->expectsJson() && $request['payment_method'] === 'test') {
                $successUrl = route('public.vacation-rental.booking.success', $booking->booking_number);
                return $this->httpResponse()
                    ->setData([
                        'checkoutUrl' => $successUrl,
                        'booking' => [
                            'id' => $booking->id,
                            'booking_number' => $booking->booking_number,
                            'total_amount' => $booking->total_amount,
                            'status' => $booking->status
                        ]
                    ])
                    ->setMessage(__('Booking successful! Test payment completed.'));
            }

            // Create a new request with payment data
            $checkoutRequest = request()->create(
                route('payments.checkout'),
                'POST',
                $paymentData,
                request()->cookies->all(),
                [],
                request()->server->all()
            );

            // Resolve the CheckoutRequest through the container with validation
            $checkoutRequestInstance = app(\Botble\RealEstate\Http\Requests\CheckoutRequest::class);
            $checkoutRequestInstance->setContainer(app());
            $checkoutRequestInstance->setRedirector(app('redirect'));
            $checkoutRequestInstance->replace($paymentData);

            $checkoutController = app(\Botble\RealEstate\Http\Controllers\Fronts\CheckoutController::class);

            $response = $checkoutController->postCheckout($checkoutRequestInstance);

            // Handle different response types
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                if ($request->expectsJson()) {
                    return $this->httpResponse()
                        ->setData(['checkoutUrl' => $response->getTargetUrl()])
                        ->setMessage(__('Redirecting to payment...'));
                }
                return $response;
            }

            // If it's a JSON response with checkout URL, handle appropriately
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $data = $response->getData(true);
                if (isset($data['checkoutUrl'])) {
                    if ($request->expectsJson()) {
                        return $this->httpResponse()
                            ->setData(['checkoutUrl' => $data['checkoutUrl']])
                            ->setMessage(__('Redirecting to payment...'));
                    }
                    return redirect($data['checkoutUrl']);
                }
            }

            // Fallback: redirect to success page for test payments
            if ($request['payment_method'] === 'test') {
                $successUrl = route('public.vacation-rental.booking.success', $booking->booking_number);
                if ($request->expectsJson()) {
                    return $this->httpResponse()
                        ->setData(['checkoutUrl' => $successUrl])
                        ->setMessage(__('Booking successful!'));
                }
                return redirect($successUrl);
            }

            return $response;

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Payment processing failed: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'payment_method' => $request['payment_method'],
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', __('Payment processing failed. Please try again.'));
        }
    }

    public function paymentCallback(Request $request)
    {
        $chargeId = $request->get('charge_id');
        $bookingId = session('vacation_rental_booking_id');

        if (!$chargeId || !$bookingId) {
            return redirect()->route('public.properties')->with('error', __('Invalid payment callback.'));
        }

        $booking = VacationRentalBooking::find($bookingId);

        if (!$booking) {
            return redirect()->route('public.properties')->with('error', __('Booking not found.'));
        }

        // Update booking with payment information
        $booking->update([
            'payment_reference' => $chargeId,
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
            'payment_status' => VacationRentalBooking::PAYMENT_PAID,
        ]);

        // Send booking confirmation email to customer
        try {
            Notification::route('mail', $booking->guest_email)
                ->notify(new VacationRentalBookingConfirmationNotification($booking));
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'customer_email' => $booking->guest_email,
            ]);
        }

        // Send booking notification email to property owner (following consultation pattern)
        $property = $booking->property;
        $sendTo = null;

        if ($property->author?->email) {
            $sendTo = $property->author->email;
            Log::info('Booking notification will be sent to property owner', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'owner_email' => $sendTo,
            ]);
        } else {
            Log::warning('No property owner email found for booking notification, will use admin fallback', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'property_id' => $property->id,
            ]);
        }

        try {
            // Use EmailHandler following consultation form pattern
            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'booking_number' => $booking->booking_number,
                    'property_name' => $property->name,
                    'property_link' => $property->url,
                    'guest_name' => $booking->guest_name,
                    'guest_email' => $booking->guest_email,
                    'guest_phone' => $booking->guest_phone ?: 'Not provided',
                    'check_in_date' => $booking->check_in_date->format('M d, Y'),
                    'check_out_date' => $booking->check_out_date->format('M d, Y'),
                    'guests_count' => $booking->guests_count,
                    'total_amount' => format_price($booking->total_amount),
                    'payment_status' => ucfirst($booking->payment_status),
                    'special_requests' => $booking->special_requests ?: 'None',
                    'booking_link' => route('public.vacation-rental.booking.details', $booking->booking_number),
                ])
                ->sendUsingTemplate('vacation_rental_booking_confirmed', $sendTo);

            Log::info('Booking notification sent successfully', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'sent_to' => $sendTo ?: 'admin fallback',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking notification: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'error' => $e->getMessage(),
            ]);
        }

        // Note: Availability is already updated to 'booked' status by the VacationRentalBooking model's created event
        // The booking dates are automatically set to STATUS_BOOKED when the booking is created
        // No additional availability updates are needed here

        // Clear session
        session()->forget(['vacation_rental_booking_id', 'vacation_rental_payment_data']);

        return redirect()->route('public.vacation-rental.booking.success', $booking->booking_number);
    }

    public function bookingSuccess($bookingNumber)
    {
        $booking = VacationRentalBooking::where('booking_number', $bookingNumber)
            ->with(['vacationRental.slugable'])
            ->firstOrFail();

        $this->pageTitle(__('Booking Confirmed'));

        return Theme::scope('real-estate.booking.success', compact('booking'))->render();
    }

    public function bookingDetails($bookingNumber)
    {
        $booking = VacationRentalBooking::where('booking_number', $bookingNumber)
            ->with(['vacationRental.slugable'])
            ->firstOrFail();

        $this->pageTitle(__('Booking Details'));

        return Theme::scope('real-estate.booking.details', compact('booking'))->render();
    }

    public function sendInquiry(VacationRentalInquiryRequest $request)
    {
        Log::info('Vacation rental inquiry started', [
            'request_data' => $request->all(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'content_type' => $request->header('Content-Type'),
            'is_json' => $request->isJson(),
            'expects_json' => $request->expectsJson(),
        ]);

        try {
            $vacationRental = VacationRental::where('id', $request->vacation_rental_id)
                ->where('moderation_status', 'approved')
                ->with('author')
                ->firstOrFail();

            // Follow the same pattern as consultation form
            $sendTo = null;
            if ($vacationRental->author?->email) {
                $sendTo = $vacationRental->author->email;
                Log::info('Inquiry will be sent to vacation rental owner', [
                    'vacation_rental_id' => $vacationRental->id,
                    'owner_email' => $sendTo,
                ]);
            } else {
                Log::warning('No vacation rental owner email found, will use admin fallback', [
                    'vacation_rental_id' => $vacationRental->id,
                ]);
            }

            // Use EmailHandler following consultation form pattern
            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'consult_name' => $request->name,
                    'consult_email' => $request->email,
                    'consult_phone' => $request->phone ?: 'Not provided',
                    'consult_content' => $request->message ?: 'Vacation rental inquiry',
                    'consult_link' => $vacationRental->url,
                    'consult_subject' => 'Vacation Rental Inquiry - ' . $vacationRental->name,
                    'consult_ip_address' => $request->ip(),
                    'property_name' => $vacationRental->name,
                    'check_in_date' => $request->check_in_date,
                    'check_out_date' => $request->check_out_date,
                    'guests_count' => $request->guests_count,
                ])
                ->sendUsingTemplate('vacation_rental_booking_inquiry', $sendTo);

            Log::info('Inquiry email sent successfully', [
                'vacation_rental_id' => $vacationRental->id,
                'customer_email' => $request->email,
                'sent_to' => $sendTo ?: 'admin fallback',
            ]);

            return $this->httpResponse()
                ->setMessage(__('Your inquiry has been sent successfully! The property owner will contact you soon.'));

        } catch (\Exception $e) {
            Log::error('Failed to send vacation rental inquiry: ' . $e->getMessage(), [
                'vacation_rental_id' => $request->vacation_rental_id,
                'customer_email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Sorry, there was an error sending your inquiry. Please try again.'));
        }
    }

    protected function generateBookingNumber(): string
    {
        do {
            $number = 'VR' . date('Y') . strtoupper(Str::random(6));
        } while (VacationRentalBooking::where('booking_number', $number)->exists());

        return $number;
    }

    public function getAvailability(Request $request, VacationRental $vacationRental)
    {
        $startDate = Carbon::parse($request->query('start'));
        $endDate = Carbon::parse($request->query('end'));
        $exceptionsOnly = $request->query('exceptions_only', false);

        if (!$startDate || !$endDate) {
            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Invalid date range.'))
                ->setCode(422);
        }

        try {
            if ($exceptionsOnly) {
                // Return only non-available dates for performance optimization
                $availability = $this->availabilityService->getAvailabilityExceptions($vacationRental, $startDate, $endDate);
            } else {
                // Return full availability data (backward compatibility)
                $availability = $this->availabilityService->getAvailabilityDetails($vacationRental, $startDate, $endDate);
            }

            return $this->httpResponse()->setData($availability);
        } catch (\Exception $e) {
            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Error fetching availability: :error', ['error' => $e->getMessage()]))
                ->setCode(500);
        }
    }

    public function calculatePrice(Request $request, VacationRental $vacationRental)
    {
        Log::info('AJAX Price Calculation Started', ['vacation_rental_id' => $vacationRental->id, 'request' => $request->all()]);

        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $guests = $request->input('guests', 1);

        if (!$checkIn || !$checkOut) {
            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Please select check-in and check-out dates.'))
                ->setCode(422);
        }

        try {
            $checkInDate = Carbon::parse($checkIn);
            $checkOutDate = Carbon::parse($checkOut);

            $pricing = $this->availabilityService->calculateBookingPrice($vacationRental, $checkInDate, $checkOutDate, $guests);

            return $this->httpResponse()->setData($pricing);
        } catch (\InvalidArgumentException $e) {
            Log::warning('Price calculation validation error.', ['exception' => $e]);
            return $this->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->setCode(422);
        } catch (\Exception $e) {
            Log::error('Price calculation failed unexpectedly.', ['exception' => $e]);
            return $this->httpResponse()
                ->setError()
                ->setMessage(__('Error calculating price: :error', ['error' => $e->getMessage()]))
                ->setCode(500);
        }
    }
}
