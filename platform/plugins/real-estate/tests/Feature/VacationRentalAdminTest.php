<?php

namespace Botble\RealEstate\Tests\Feature;

use Botble\ACL\Models\User;
use Botble\ACL\Services\ActivateUserService;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class VacationRentalAdminTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $availabilityService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdminUser();
        $this->actingAs($this->admin);

        // Mock the availability service
        $this->availabilityService = Mockery::mock(AvailabilityService::class);
        $this->app->instance(AvailabilityService::class, $this->availabilityService);
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_vacation_rental_dashboard_displays_correctly()
    {
        // Create test data
        Property::factory()->count(3)->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);
        VacationRentalBooking::factory()->count(5)->create();

        $response = $this->get(route('vacation-rental.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::vacation-rental.dashboard');
        $response->assertViewHas([
            'totalProperties',
            'totalBookings',
            'activeBookings',
            'monthlyRevenue',
            'recentBookings',
            'upcomingCheckIns',
            'propertiesNeedingAttention',
        ]);
    }

    public function test_vacation_rental_properties_index_displays_correctly()
    {
        Property::factory()->count(3)->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $response = $this->get(route('vacation-rental.index'));

        $response->assertStatus(200);
        $response->assertViewIs('core/table::table');
    }

    public function test_vacation_rental_bookings_index_displays_correctly()
    {
        VacationRentalBooking::factory()->count(3)->create();

        $response = $this->get(route('vacation-rental.bookings'));

        $response->assertStatus(200);
        $response->assertViewIs('core/table::table');
    }

    public function test_availability_management_page_displays_correctly()
    {
        $property = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $response = $this->get(route('vacation-rental.availability'));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::vacation-rental.availability');
        $response->assertViewHas(['properties', 'selectedProperty', 'availabilityData', 'calendarEvents']);
    }

    public function test_availability_management_with_selected_property()
    {
        $property = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $this->availabilityService
            ->shouldReceive('getAvailabilityDetails')
            ->once()
            ->andReturn(['availability' => []]);

        $this->availabilityService
            ->shouldReceive('getCalendarEvents')
            ->once()
            ->andReturn(['events' => []]);

        $response = $this->get(route('vacation-rental.availability', ['property_id' => $property->id]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedProperty', $property);
    }

    public function test_calendar_view_displays_correctly()
    {
        $property = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $response = $this->get(route('vacation-rental.calendar'));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::vacation-rental.calendar');
        $response->assertViewHas(['properties', 'selectedProperty', 'monthlyData']);
    }

    public function test_calendar_view_with_selected_property()
    {
        $property = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $this->availabilityService
            ->shouldReceive('getMonthlyAvailabilitySummary')
            ->once()
            ->andReturn(['summary' => []]);

        $response = $this->get(route('vacation-rental.calendar', [
            'property_id' => $property->id,
            'year' => 2024,
            'month' => 12,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedProperty', $property);
    }

    public function test_block_dates_functionality()
    {
        $property = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $this->availabilityService
            ->shouldReceive('blockDates')
            ->once()
            ->with(
                $property->id,
                Mockery::type(Carbon::class),
                Mockery::type(Carbon::class),
                'Test reason'
            );

        $data = [
            'property_id' => $property->id,
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'reason' => 'Test reason',
        ];

        $response = $this->post(route('vacation-rental.block-dates'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => __('Dates blocked successfully'),
        ]);
    }

    public function test_unblock_dates_functionality()
    {
        $property = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $this->availabilityService
            ->shouldReceive('unblockDates')
            ->once()
            ->with(
                $property->id,
                Mockery::type(Carbon::class),
                Mockery::type(Carbon::class)
            );

        $data = [
            'property_id' => $property->id,
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
        ];

        $response = $this->post(route('vacation-rental.unblock-dates'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => __('Dates unblocked successfully'),
        ]);
    }

    public function test_block_dates_validation_fails_with_invalid_property()
    {
        $data = [
            'property_id' => 999999, // Non-existent property
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
        ];

        $response = $this->post(route('vacation-rental.block-dates'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['property_id']);
    }

    public function test_block_dates_validation_fails_with_invalid_dates()
    {
        $property = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $data = [
            'property_id' => $property->id,
            'start_date' => 'invalid-date',
            'end_date' => 'invalid-date',
        ];

        $response = $this->post(route('vacation-rental.block-dates'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['start_date', 'end_date']);
    }

    public function test_block_dates_validation_fails_when_end_date_before_start_date()
    {
        $property = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $data = [
            'property_id' => $property->id,
            'start_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->format('Y-m-d'), // Before start date
        ];

        $response = $this->post(route('vacation-rental.block-dates'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_date']);
    }

    public function test_get_availability_data_api_endpoint()
    {
        $property = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);

        $this->availabilityService
            ->shouldReceive('getAvailabilityDetails')
            ->once()
            ->andReturn(['availability' => ['test' => 'data']]);

        $data = [
            'property_id' => $property->id,
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->get(route('vacation-rental.availability-data', $data));

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'data' => ['availability' => ['test' => 'data']],
        ]);
    }

    public function test_get_availability_data_validation_fails_with_missing_parameters()
    {
        $response = $this->get(route('vacation-rental.availability-data'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['property_id', 'start_date', 'end_date']);
    }

    public function test_admin_can_only_manage_vacation_rental_properties()
    {
        $vacationRental = Property::factory()->create(['type' => PropertyTypeEnum::VACATION_RENTAL]);
        $regularProperty = Property::factory()->create(['type' => PropertyTypeEnum::SALE]);

        // Test blocking dates on vacation rental - should work
        $this->availabilityService
            ->shouldReceive('blockDates')
            ->once();

        $data = [
            'property_id' => $vacationRental->id,
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
        ];

        $response = $this->post(route('vacation-rental.block-dates'), $data);
        $response->assertStatus(200);

        // Test blocking dates on regular property - should fail
        $data['property_id'] = $regularProperty->id;

        $response = $this->post(route('vacation-rental.block-dates'), $data);
        $response->assertStatus(404); // Property not found (filtered by type)
    }
}
