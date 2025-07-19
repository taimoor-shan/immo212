<?php

namespace Botble\RealEstate\Tests\Feature;

use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Models\PropertyAvailability;
use Botble\RealEstate\Services\AvailabilityService;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VacationRentalTest extends TestCase
{
    use RefreshDatabase;

    protected Property $vacationRental;
    protected AvailabilityService $availabilityService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->availabilityService = app(AvailabilityService::class);
        
        // Create a vacation rental property for testing
        $this->vacationRental = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'price' => 150.00,
            'minimum_stay' => 2,
            'maximum_stay' => 14,
            'maximum_guests' => 6,
            'cleaning_fee' => 50.00,
            'security_deposit' => 200.00,
            'check_in_time' => '15:00',
            'check_out_time' => '11:00',
            'house_rules' => 'No smoking, No pets',
            'cancellation_policy' => 'moderate',
        ]);
    }

    /** @test */
    public function it_can_check_availability_for_vacation_rental()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(3);

        $isAvailable = $this->availabilityService->checkAvailability(
            $this->vacationRental->id,
            $checkIn,
            $checkOut
        );

        $this->assertTrue($isAvailable);
    }

    /** @test */
    public function it_can_calculate_booking_price()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(3);
        $guests = 4;

        $pricing = $this->availabilityService->calculateBookingPrice(
            $this->vacationRental->id,
            $checkIn,
            $checkOut,
            $guests
        );

        $this->assertIsArray($pricing);
        $this->assertArrayHasKey('nights', $pricing);
        $this->assertArrayHasKey('base_price_per_night', $pricing);
        $this->assertArrayHasKey('total_nights_cost', $pricing);
        $this->assertArrayHasKey('cleaning_fee', $pricing);
        $this->assertArrayHasKey('total_amount', $pricing);
        
        $this->assertEquals(3, $pricing['nights']);
        $this->assertEquals(150.00, $pricing['base_price_per_night']);
        $this->assertEquals(450.00, $pricing['total_nights_cost']);
        $this->assertEquals(50.00, $pricing['cleaning_fee']);
    }

    /** @test */
    public function it_validates_minimum_stay_requirement()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDay(); // Only 1 night

        $isValid = $this->availabilityService->validateMinimumStay(
            $this->vacationRental->id,
            $checkIn,
            $checkOut
        );

        $this->assertFalse($isValid);

        // Test with valid minimum stay
        $checkOut = $checkIn->copy()->addDays(2); // 2 nights
        $isValid = $this->availabilityService->validateMinimumStay(
            $this->vacationRental->id,
            $checkIn,
            $checkOut
        );

        $this->assertTrue($isValid);
    }

    /** @test */
    public function it_can_block_and_unblock_dates()
    {
        $startDate = Carbon::tomorrow();
        $endDate = $startDate->copy()->addDays(3);

        // Block dates
        $this->availabilityService->blockDates(
            $this->vacationRental->id,
            $startDate,
            $endDate,
            'Maintenance'
        );

        // Check that dates are blocked
        $isAvailable = $this->availabilityService->checkAvailability(
            $this->vacationRental->id,
            $startDate,
            $endDate
        );

        $this->assertFalse($isAvailable);

        // Unblock dates
        $this->availabilityService->unblockDates(
            $this->vacationRental->id,
            $startDate,
            $endDate
        );

        // Check that dates are available again
        $isAvailable = $this->availabilityService->checkAvailability(
            $this->vacationRental->id,
            $startDate,
            $endDate
        );

        $this->assertTrue($isAvailable);
    }

    /** @test */
    public function it_can_create_vacation_rental_booking()
    {
        $bookingData = [
            'booking_number' => 'VR2024TEST01',
            'property_id' => $this->vacationRental->id,
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
            'guest_phone' => '+1234567890',
            'check_in_date' => Carbon::tomorrow(),
            'check_out_date' => Carbon::tomorrow()->addDays(3),
            'nights_count' => 3,
            'guests_count' => 4,

            'base_price_per_night' => 150.00,
            'total_nights_cost' => 450.00,
            'cleaning_fee' => 50.00,
            'service_fee' => 0.00,
            'taxes' => 0.00,
            'security_deposit' => 200.00,
            'total_amount' => 500.00,
            'special_requests' => 'Late check-in',
            'status' => VacationRentalBooking::STATUS_PENDING,
            'payment_status' => VacationRentalBooking::PAYMENT_PENDING,
        ];

        $booking = VacationRentalBooking::create($bookingData);

        $this->assertInstanceOf(VacationRentalBooking::class, $booking);
        $this->assertEquals('VR2024TEST01', $booking->booking_number);
        $this->assertEquals($this->vacationRental->id, $booking->property_id);
        $this->assertEquals('John Doe', $booking->guest_name);
        $this->assertEquals('john@example.com', $booking->guest_email);
        $this->assertEquals(3, $booking->nights_count);
        $this->assertEquals(4, $booking->guests_count);
        $this->assertEquals(500.00, $booking->total_amount);
    }

    /** @test */
    public function it_can_get_monthly_availability_summary()
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $summary = $this->availabilityService->getMonthlyAvailabilitySummary(
            $this->vacationRental->id,
            $year,
            $month
        );

        $this->assertIsArray($summary);
        $this->assertArrayHasKey('summary', $summary);
        $this->assertArrayHasKey('availability', $summary);
        $this->assertArrayHasKey('events', $summary);
        
        $this->assertArrayHasKey('available_days', $summary['summary']);
        $this->assertArrayHasKey('booked_days', $summary['summary']);
        $this->assertArrayHasKey('blocked_days', $summary['summary']);
        $this->assertArrayHasKey('occupancy_rate', $summary['summary']);
    }

    /** @test */
    public function it_prevents_double_booking()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(3);

        // Create first booking
        VacationRentalBooking::create([
            'booking_number' => 'VR2024TEST01',
            'property_id' => $this->vacationRental->id,
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'nights_count' => 3,
            'guests_count' => 2,
            'base_price_per_night' => 150.00,
            'total_nights_cost' => 450.00,
            'cleaning_fee' => 50.00,
            'total_amount' => 500.00,
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
            'payment_status' => VacationRentalBooking::PAYMENT_PAID,
        ]);

        // Block the dates for the booking
        $this->availabilityService->blockDates(
            $this->vacationRental->id,
            $checkIn,
            $checkOut,
            'Booking VR2024TEST01'
        );

        // Try to check availability for overlapping dates
        $isAvailable = $this->availabilityService->checkAvailability(
            $this->vacationRental->id,
            $checkIn,
            $checkOut
        );

        $this->assertFalse($isAvailable);
    }

    /** @test */
    public function it_can_get_availability_details()
    {
        $startDate = Carbon::tomorrow();
        $endDate = $startDate->copy()->addDays(7);

        $details = $this->availabilityService->getAvailabilityDetails(
            $this->vacationRental->id,
            $startDate,
            $endDate
        );

        $this->assertIsArray($details);
        
        // Check that we have details for each day
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $this->assertArrayHasKey($dateKey, $details);
            
            $dayInfo = $details[$dateKey];
            $this->assertArrayHasKey('status', $dayInfo);
            $this->assertArrayHasKey('price', $dayInfo);
            $this->assertArrayHasKey('minimum_stay', $dayInfo);
            
            $currentDate->addDay();
        }
    }
}
