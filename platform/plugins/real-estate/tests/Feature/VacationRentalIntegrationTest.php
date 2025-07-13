<?php

namespace Botble\RealEstate\Tests\Feature;

use Botble\ACL\Models\User;
use Botble\ACL\Services\ActivateUserService;
use Botble\Base\Facades\EmailHandler;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\PropertyAvailability;
use Botble\RealEstate\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class VacationRentalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $property;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        Mail::fake();
        
        $this->property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'name' => 'Beachfront Villa',
            'price' => 200.00,
            'minimum_stay' => 3,
            'maximum_guests' => 6,
            'check_in_time' => '15:00',
            'check_out_time' => '11:00',
        ]);

        $this->admin = $this->createAdminUser();
    }

    public function test_complete_booking_inquiry_flow()
    {
        // Step 1: Create availability for the property
        $checkInDate = Carbon::tomorrow();
        $checkOutDate = $checkInDate->copy()->addDays(4);
        
        $this->createAvailabilityForDateRange($this->property->id, $checkInDate, $checkOutDate);

        // Step 2: Submit booking inquiry
        $bookingData = [
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'phone' => '+1-555-123-4567',
            'content' => 'We are planning a family vacation and would love to stay at your beautiful property.',
            'property_id' => $this->property->id,
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'check_out_date' => $checkOutDate->format('Y-m-d'),
            'guests_count' => 4,
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $bookingData);

        // Step 3: Verify booking inquiry was successful
        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => __('Your booking inquiry has been sent successfully. We will contact you soon.'),
        ]);

        // Step 4: Verify consult record was created
        $this->assertDatabaseHas('re_consults', [
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'property_id' => $this->property->id,
        ]);

        $consult = Consult::where('property_id', $this->property->id)->first();
        $this->assertNotNull($consult);
        $this->assertStringContainsString('Booking Details', $consult->content);
        $this->assertStringContainsString('Check-in Date:', $consult->content);
        $this->assertStringContainsString('Number of Guests: 4', $consult->content);

        // Step 5: Test admin can view the inquiry
        $this->actingAs($this->admin);
        
        $dashboardResponse = $this->get(route('vacation-rental.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertViewHas('recentBookings');

        // Step 6: Test availability management
        $availabilityResponse = $this->get(route('vacation-rental.availability', [
            'property_id' => $this->property->id
        ]));
        $availabilityResponse->assertStatus(200);
        $availabilityResponse->assertViewHas('selectedProperty', $this->property);

        // Step 7: Test date blocking functionality
        $blockDatesData = [
            'property_id' => $this->property->id,
            'start_date' => Carbon::tomorrow()->addDays(10)->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(12)->format('Y-m-d'),
            'reason' => 'Maintenance period',
        ];

        $blockResponse = $this->post(route('vacation-rental.block-dates'), $blockDatesData);
        $blockResponse->assertStatus(200);
        $blockResponse->assertJson(['error' => false]);
    }

    public function test_booking_inquiry_validation_prevents_invalid_submissions()
    {
        // Test 1: Invalid property type
        $regularProperty = Property::factory()->create([
            'type' => PropertyTypeEnum::SALE
        ]);

        $invalidData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test',
            'property_id' => $regularProperty->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $invalidData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['property_id']);

        // Test 2: Insufficient minimum stay
        $shortStayData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test',
            'property_id' => $this->property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(1)->format('Y-m-d'), // Only 1 night, minimum is 3
            'guests_count' => 2,
        ];

        $this->createAvailabilityForDateRange($this->property->id, Carbon::tomorrow(), Carbon::tomorrow()->addDays(1));

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $shortStayData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['check_out_date']);

        // Test 3: Too many guests
        $tooManyGuestsData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test',
            'property_id' => $this->property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(4)->format('Y-m-d'),
            'guests_count' => 10, // Exceeds maximum of 6
        ];

        $this->createAvailabilityForDateRange($this->property->id, Carbon::tomorrow(), Carbon::tomorrow()->addDays(4));

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $tooManyGuestsData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['guests_count']);
    }

    public function test_admin_interface_displays_correct_data()
    {
        $this->actingAs($this->admin);

        // Create some test data
        Consult::factory()->count(3)->create([
            'property_id' => $this->property->id,
        ]);

        // Test dashboard
        $dashboardResponse = $this->get(route('vacation-rental.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertViewHas([
            'totalProperties',
            'totalBookings',
            'activeBookings',
            'monthlyRevenue',
        ]);

        // Test properties listing
        $propertiesResponse = $this->get(route('vacation-rental.index'));
        $propertiesResponse->assertStatus(200);

        // Test availability calendar
        $calendarResponse = $this->get(route('vacation-rental.calendar'));
        $calendarResponse->assertStatus(200);
        $calendarResponse->assertViewHas(['properties', 'selectedProperty', 'monthlyData']);
    }

    public function test_email_template_variables_are_correctly_set()
    {
        $checkInDate = Carbon::tomorrow();
        $checkOutDate = $checkInDate->copy()->addDays(3);
        
        $this->createAvailabilityForDateRange($this->property->id, $checkInDate, $checkOutDate);

        $bookingData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+1-555-987-6543',
            'content' => 'Looking forward to our stay!',
            'property_id' => $this->property->id,
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'check_out_date' => $checkOutDate->format('Y-m-d'),
            'guests_count' => 3,
        ];

        // Mock EmailHandler to capture variables
        EmailHandler::shouldReceive('setModule')
            ->once()
            ->andReturnSelf();

        EmailHandler::shouldReceive('setVariableValues')
            ->once()
            ->with(\Mockery::on(function ($variables) {
                return isset($variables['consult_name']) &&
                       isset($variables['property_name']) &&
                       isset($variables['check_in_date']) &&
                       isset($variables['check_out_date']) &&
                       isset($variables['guests_count']) &&
                       $variables['consult_name'] === 'Jane Doe' &&
                       $variables['property_name'] === 'Beachfront Villa' &&
                       $variables['guests_count'] === 3;
            }))
            ->andReturnSelf();

        EmailHandler::shouldReceive('sendUsingTemplate')
            ->once()
            ->with('vacation_rental_booking_inquiry', \Mockery::any());

        $response = $this->post(route('public.vacation-rental.booking-inquiry'), $bookingData);
        $response->assertStatus(200);
    }

    public function test_property_template_conditionally_shows_booking_form()
    {
        // Test vacation rental property shows booking form
        $vacationRentalResponse = $this->get(route('public.property', $this->property->slug));
        $vacationRentalResponse->assertStatus(200);
        $vacationRentalResponse->assertSee('vacation-rental-booking');

        // Test regular property shows contact form
        $regularProperty = Property::factory()->create([
            'type' => PropertyTypeEnum::SALE
        ]);

        $regularPropertyResponse = $this->get(route('public.property', $regularProperty->slug));
        $regularPropertyResponse->assertStatus(200);
        $regularPropertyResponse->assertDontSee('vacation-rental-booking');
    }

    protected function createAdminUser(): User
    {
        $user = new User();
        $user->forceFill([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@test.com',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'super_user' => 1,
            'manage_supers' => 1,
        ]);
        $user->save();

        app(ActivateUserService::class)->activate($user);

        return $user;
    }

    protected function createAvailabilityForDateRange(int $propertyId, Carbon $startDate, Carbon $endDate): void
    {
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate->subDay());

        foreach ($period as $date) {
            PropertyAvailability::create([
                'property_id' => $propertyId,
                'date' => $date->format('Y-m-d'),
                'status' => PropertyAvailability::STATUS_AVAILABLE,
                'price' => 200.00,
            ]);
        }
    }
}
