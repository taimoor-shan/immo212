<?php

namespace Botble\RealEstate\Tests\Feature;

use Botble\ACL\Models\User;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VacationRentalDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected Property $vacationRental;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an account for testing
        $this->account = Account::factory()->create([
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Create a vacation rental property owned by the account
        $this->vacationRental = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'author_id' => $this->account->id,
            'author_type' => Account::class,
            'price' => 150.00,
            'minimum_stay' => 2,
            'maximum_guests' => 6,
            'cleaning_fee' => 50.00,
        ]);
    }

    /** @test */
    public function it_requires_authentication_for_dashboard_access()
    {
        $response = $this->get(route('public.account.vacation-rentals.dashboard'));

        $response->assertRedirect(route('public.account.login'));
    }

    /** @test */
    public function it_can_display_vacation_rental_dashboard()
    {
        $this->actingAs($this->account, 'account');

        $response = $this->get(route('public.account.vacation-rentals.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::themes.dashboard.vacation-rentals.index');
        $response->assertViewHas('vacationRentals');
        $response->assertViewHas('totalProperties');
        $response->assertViewHas('totalBookings');
        $response->assertViewHas('monthlyRevenue');
    }

    /** @test */
    public function it_can_display_bookings_page()
    {
        $this->actingAs($this->account, 'account');

        // Create some bookings
        VacationRentalBooking::factory()->count(3)->create([
            'property_id' => $this->vacationRental->id,
        ]);

        $response = $this->get(route('public.account.vacation-rentals.bookings'));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::themes.dashboard.vacation-rentals.bookings');
        $response->assertViewHas('bookings');
        $response->assertViewHas('properties');
    }

    /** @test */
    public function it_can_filter_bookings_by_status()
    {
        $this->actingAs($this->account, 'account');

        // Create bookings with different statuses
        VacationRentalBooking::factory()->create([
            'property_id' => $this->vacationRental->id,
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
        ]);

        VacationRentalBooking::factory()->create([
            'property_id' => $this->vacationRental->id,
            'status' => VacationRentalBooking::STATUS_PENDING,
        ]);

        $response = $this->get(route('public.account.vacation-rentals.bookings', [
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
        ]));

        $response->assertStatus(200);
        $bookings = $response->viewData('bookings');
        
        foreach ($bookings as $booking) {
            $this->assertEquals(VacationRentalBooking::STATUS_CONFIRMED, $booking->status);
        }
    }

    /** @test */
    public function it_can_display_availability_management_page()
    {
        $this->actingAs($this->account, 'account');

        $response = $this->get(route('public.account.vacation-rentals.availability'));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::themes.dashboard.vacation-rentals.availability');
        $response->assertViewHas('properties');
    }

    /** @test */
    public function it_can_display_availability_for_specific_property()
    {
        $this->actingAs($this->account, 'account');

        $response = $this->get(route('public.account.vacation-rentals.availability', [
            'property_id' => $this->vacationRental->id,
            'month' => Carbon::now()->format('Y-m'),
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedProperty', $this->vacationRental);
        $response->assertViewHas('availabilityData');
    }

    /** @test */
    public function it_can_display_calendar_view()
    {
        $this->actingAs($this->account, 'account');

        $response = $this->get(route('public.account.vacation-rentals.calendar'));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::themes.dashboard.vacation-rentals.calendar');
        $response->assertViewHas('properties');
    }

    /** @test */
    public function it_can_block_dates()
    {
        $this->actingAs($this->account, 'account');

        $requestData = [
            'property_id' => $this->vacationRental->id,
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'reason' => 'Maintenance',
        ];

        $response = $this->post(route('public.account.vacation-rentals.block-dates'), $requestData);

        $response->assertStatus(200);
        $response->assertJson(['error' => false]);
        
        // Check that availability records were created
        $this->assertDatabaseHas('re_property_availability', [
            'property_id' => $this->vacationRental->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'status' => 'blocked',
        ]);
    }

    /** @test */
    public function it_can_unblock_dates()
    {
        $this->actingAs($this->account, 'account');

        // First block some dates
        $startDate = Carbon::tomorrow();
        $endDate = $startDate->copy()->addDays(3);
        
        app(\Botble\RealEstate\Services\AvailabilityService::class)->blockDates(
            $this->vacationRental->id,
            $startDate,
            $endDate,
            'Test block'
        );

        $requestData = [
            'property_id' => $this->vacationRental->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];

        $response = $this->post(route('public.account.vacation-rentals.unblock-dates'), $requestData);

        $response->assertStatus(200);
        $response->assertJson(['error' => false]);
    }

    /** @test */
    public function it_can_update_booking_status()
    {
        $this->actingAs($this->account, 'account');

        $booking = VacationRentalBooking::factory()->create([
            'property_id' => $this->vacationRental->id,
            'status' => VacationRentalBooking::STATUS_PENDING,
        ]);

        $response = $this->put(route('public.account.vacation-rentals.bookings.update-status', $booking->id), [
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['error' => false]);
        
        $booking->refresh();
        $this->assertEquals(VacationRentalBooking::STATUS_CONFIRMED, $booking->status);
    }

    /** @test */
    public function it_prevents_access_to_other_users_properties()
    {
        $this->actingAs($this->account, 'account');

        // Create another account and property
        $otherAccount = Account::factory()->create();
        $otherProperty = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'author_id' => $otherAccount->id,
            'author_type' => Account::class,
        ]);

        $requestData = [
            'property_id' => $otherProperty->id,
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'reason' => 'Unauthorized attempt',
        ];

        $response = $this->post(route('public.account.vacation-rentals.block-dates'), $requestData);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_prevents_access_to_other_users_bookings()
    {
        $this->actingAs($this->account, 'account');

        // Create another account and property
        $otherAccount = Account::factory()->create();
        $otherProperty = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'author_id' => $otherAccount->id,
            'author_type' => Account::class,
        ]);

        $otherBooking = VacationRentalBooking::factory()->create([
            'property_id' => $otherProperty->id,
        ]);

        $response = $this->put(route('public.account.vacation-rentals.bookings.update-status', $otherBooking->id), [
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_validates_block_dates_request()
    {
        $this->actingAs($this->account, 'account');

        $response = $this->post(route('public.account.vacation-rentals.block-dates'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['property_id', 'start_date', 'end_date']);
    }

    /** @test */
    public function it_validates_date_logic_for_blocking()
    {
        $this->actingAs($this->account, 'account');

        $requestData = [
            'property_id' => $this->vacationRental->id,
            'start_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->format('Y-m-d'), // End before start
        ];

        $response = $this->post(route('public.account.vacation-rentals.block-dates'), $requestData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_date']);
    }
}
