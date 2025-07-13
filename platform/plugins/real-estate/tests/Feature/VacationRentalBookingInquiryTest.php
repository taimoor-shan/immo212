<?php

namespace Botble\RealEstate\Tests\Feature;

use Botble\Base\Facades\EmailHandler;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Mockery;

class VacationRentalBookingInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected $availabilityService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the availability service
        $this->availabilityService = Mockery::mock(AvailabilityService::class);
        $this->app->instance(AvailabilityService::class, $this->availabilityService);
        
        Mail::fake();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_successful_booking_inquiry_submission()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'name' => 'Beautiful Beach House',
            'maximum_guests' => 4,
            'minimum_stay' => 2,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andReturn(true);

        $this->availabilityService
            ->shouldReceive('validateMinimumStay')
            ->once()
            ->andReturn(true);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'content' => 'I would like to book this property for a family vacation.',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => __('Your booking inquiry has been sent successfully. We will contact you soon.'),
        ]);

        // Check that consult record was created
        $this->assertDatabaseHas('re_consults', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'property_id' => $property->id,
        ]);

        // Check that booking details were added to content
        $consult = Consult::where('property_id', $property->id)->first();
        $this->assertStringContainsString('Booking Details', $consult->content);
        $this->assertStringContainsString('Check-in Date:', $consult->content);
        $this->assertStringContainsString('Check-out Date:', $consult->content);
        $this->assertStringContainsString('Number of Guests: 2', $consult->content);
    }

    public function test_booking_inquiry_fails_for_non_vacation_rental_property()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::SALE, // Not vacation rental
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['property_id']);
    }

    public function test_booking_inquiry_fails_for_unavailable_dates()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andReturn(false);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['check_in_date']);
    }

    public function test_booking_inquiry_fails_for_insufficient_minimum_stay()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'minimum_stay' => 5,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andReturn(true);

        $this->availabilityService
            ->shouldReceive('validateMinimumStay')
            ->once()
            ->andReturn(false);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'), // Only 2 nights
            'guests_count' => 2,
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['check_out_date']);
    }

    public function test_booking_inquiry_fails_for_too_many_guests()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'maximum_guests' => 2,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andReturn(true);

        $this->availabilityService
            ->shouldReceive('validateMinimumStay')
            ->once()
            ->andReturn(true);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 4, // Exceeds maximum
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['guests_count']);
    }

    public function test_booking_inquiry_fails_with_missing_required_fields()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
        ]);

        $data = [
            'property_id' => $property->id,
            // Missing required fields
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'content',
            'check_in_date',
            'check_out_date',
            'guests_count',
        ]);
    }

    public function test_booking_inquiry_fails_with_invalid_date_format()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => 'invalid-date',
            'check_out_date' => 'another-invalid-date',
            'guests_count' => 2,
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'check_in_date',
            'check_out_date',
        ]);
    }

    public function test_booking_inquiry_fails_with_past_dates()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::yesterday()->format('Y-m-d'),
            'check_out_date' => Carbon::today()->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['check_in_date']);
    }

    public function test_booking_inquiry_handles_custom_fields()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andReturn(true);

        $this->availabilityService
            ->shouldReceive('validateMinimumStay')
            ->once()
            ->andReturn(true);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
            'consult_custom_fields' => [
                '1' => 'Special dietary requirements',
                '2' => 'Anniversary celebration',
            ],
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(200);

        // Check that custom fields were saved
        $consult = Consult::where('property_id', $property->id)->first();
        $this->assertNotNull($consult->custom_fields);
    }

    public function test_booking_inquiry_handles_exception_gracefully()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andThrow(new \Exception('Service unavailable'));

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => true,
            'message' => __('An error occurred while sending your booking inquiry. Please try again.'),
        ]);
    }
}
