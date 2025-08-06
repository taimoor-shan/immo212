<?php

namespace Botble\RealEstate\Tests\Feature;

use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\Slug\Models\Slug;
use Botble\Slug\Facades\SlugHelper;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class VacationRentalBookingTest extends TestCase
{
    use RefreshDatabase;

    protected Property $vacationRental;
    protected Slug $propertySlug;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a vacation rental property for testing
        $this->vacationRental = Property::factory()->create([
            'name' => 'Test Vacation Rental',
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'price' => 150.00,
            'minimum_stay' => 2,
            'maximum_guests' => 6,
            'cleaning_fee' => 50.00,
            'security_deposit' => 200.00,
            'moderation_status' => 'approved',
        ]);

        // Create a slug for the property
        $this->propertySlug = Slug::create([
            'reference_type' => Property::class,
            'reference_id' => $this->vacationRental->id,
            'key' => Str::slug($this->vacationRental->name),
            'prefix' => SlugHelper::getPrefix(Property::class) ?: 'properties',
        ]);
    }

    /** @test */
    public function it_can_display_booking_form_with_valid_parameters()
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(3)->format('Y-m-d');
        $guests = 4;

        $response = $this->get(route('public.vacation-rental.booking.form', [
            'slug' => $this->propertySlug->key,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => $guests,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::themes.booking.form');
        $response->assertViewHas('property', $this->vacationRental);
        $response->assertViewHas('pricing');
    }

    /** @test */
    public function it_redirects_when_dates_are_missing()
    {
        $response = $this->get(route('public.vacation-rental.booking.form', [
            'slug' => $this->propertySlug->key,
        ]));

        $response->assertRedirect($this->vacationRental->url);
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_redirects_when_dates_are_invalid()
    {
        $checkIn = Carbon::yesterday()->format('Y-m-d'); // Past date
        $checkOut = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->get(route('public.vacation-rental.booking.form', [
            'slug' => $this->propertySlug->key,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
        ]));

        $response->assertRedirect($this->vacationRental->url);
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_redirects_when_minimum_stay_not_met()
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDay()->format('Y-m-d'); // Only 1 night

        $response = $this->get(route('public.vacation-rental.booking.form', [
            'slug' => $this->propertySlug->key,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
        ]));

        $response->assertRedirect($this->vacationRental->url);
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_redirects_when_exceeding_maximum_guests()
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(3)->format('Y-m-d');
        $guests = 10; // Exceeds maximum of 6

        $response = $this->get(route('public.vacation-rental.booking.form', [
            'slug' => $this->propertySlug->key,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => $guests,
        ]));

        $response->assertRedirect($this->vacationRental->url);
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_can_process_valid_booking()
    {
        $bookingData = [
            'property_id' => $this->vacationRental->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'guests_count' => 4,

            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
            'guest_phone' => '+1234567890',
            'special_requests' => 'Late check-in please',
            'payment_method' => 'bank_transfer',
            'terms_accepted' => '1',
        ];

        $response = $this->post(route('public.vacation-rental.booking.process'), $bookingData);

        // Should redirect to payment (checkout)
        $response->assertStatus(302);
        
        // Check that booking was created
        $this->assertDatabaseHas('re_vacation_rental_bookings', [
            'property_id' => $this->vacationRental->id,
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
            'guests_count' => 4,
            'status' => VacationRentalBooking::STATUS_PENDING,
        ]);
    }

    /** @test */
    public function it_validates_required_booking_fields()
    {
        $response = $this->post(route('public.vacation-rental.booking.process'), []);

        $response->assertSessionHasErrors([
            'property_id',
            'check_in_date',
            'check_out_date',
            'guests_count',
            'guest_name',
            'guest_email',
            'payment_method',
            'terms_accepted',
        ]);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $bookingData = [
            'property_id' => $this->vacationRental->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'guests_count' => 2,
            'guest_name' => 'John Doe',
            'guest_email' => 'invalid-email',
            'payment_method' => 'bank_transfer',
            'terms_accepted' => '1',
        ];

        $response = $this->post(route('public.vacation-rental.booking.process'), $bookingData);

        $response->assertSessionHasErrors(['guest_email']);
    }

    /** @test */
    public function it_validates_date_logic()
    {
        $bookingData = [
            'property_id' => $this->vacationRental->id,
            'check_in_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->format('Y-m-d'), // Check-out before check-in
            'guests_count' => 2,
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
            'payment_method' => 'bank_transfer',
            'terms_accepted' => '1',
        ];

        $response = $this->post(route('public.vacation-rental.booking.process'), $bookingData);

        $response->assertSessionHasErrors(['check_out_date']);
    }

    /** @test */
    public function it_can_display_booking_success_page()
    {
        $booking = VacationRentalBooking::factory()->create([
            'property_id' => $this->vacationRental->id,
            'booking_number' => 'VR2024TEST01',
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
        ]);

        $response = $this->get(route('public.vacation-rental.booking.success', $booking->booking_number));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::themes.booking.success');
        $response->assertViewHas('booking', $booking);
    }

    /** @test */
    public function it_can_display_booking_details_page()
    {
        $booking = VacationRentalBooking::factory()->create([
            'property_id' => $this->vacationRental->id,
            'booking_number' => 'VR2024TEST01',
        ]);

        $response = $this->get(route('public.vacation-rental.booking.details', $booking->booking_number));

        $response->assertStatus(200);
        $response->assertViewIs('plugins/real-estate::themes.booking.details');
        $response->assertViewHas('booking', $booking);
    }

    /** @test */
    public function it_returns_404_for_non_existent_booking()
    {
        $response = $this->get(route('public.vacation-rental.booking.success', 'NONEXISTENT'));

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_get_availability_data_via_ajax()
    {
        $startDate = Carbon::tomorrow()->format('Y-m-d');
        $endDate = Carbon::tomorrow()->addDays(7)->format('Y-m-d');

        $response = $this->getJson(route('public.ajax.vacation-rentals.availability', [
            'property_id' => $this->vacationRental->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'error',
            'data',
        ]);
    }

    /** @test */
    public function it_can_calculate_price_via_ajax()
    {
        $requestData = [
            'property_id' => $this->vacationRental->id,
            'check_in' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'guests' => 4,
        ];

        $response = $this->postJson(route('public.ajax.vacation-rentals.calculate-price'), $requestData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'error',
            'data' => [
                'pricing' => [
                    'nights',
                    'base_price_per_night',
                    'total_nights_cost',
                    'cleaning_fee',
                    'total_amount',
                ],
                'property',
            ],
        ]);
    }
}
