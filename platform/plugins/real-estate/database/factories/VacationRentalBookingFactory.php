<?php

namespace Botble\RealEstate\Database\Factories;

use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\VacationRentalBooking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class VacationRentalBookingFactory extends Factory
{
    protected $model = VacationRentalBooking::class;

    public function definition(): array
    {
        $checkInDate = $this->faker->dateTimeBetween('now', '+30 days');
        $checkOutDate = (clone $checkInDate)->modify('+' . $this->faker->numberBetween(2, 14) . ' days');
        $guestsCount = $this->faker->numberBetween(1, 6);
        $pricePerNight = $this->faker->randomFloat(2, 50, 500);
        $nights = Carbon::parse($checkInDate)->diffInDays(Carbon::parse($checkOutDate));
        
        return [
            'booking_number' => 'VR-' . strtoupper($this->faker->bothify('??##??##')),
            'property_id' => Property::factory(),
            'guest_name' => $this->faker->name,
            'guest_email' => $this->faker->safeEmail,
            'guest_phone' => $this->faker->phoneNumber,
            'guest_address' => $this->faker->address,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'nights_count' => $nights,
            'guests_count' => $guestsCount,

            'base_price_per_night' => $pricePerNight,
            'total_nights_cost' => $pricePerNight * $nights,
            'cleaning_fee' => $this->faker->randomFloat(2, 0, 100),
            'security_deposit' => $this->faker->randomFloat(2, 0, 500),
            'service_fee' => $this->faker->randomFloat(2, 10, 50),
            'taxes' => $this->faker->randomFloat(2, 0, 100),
            'total_amount' => function (array $attributes) {
                return $attributes['total_nights_cost'] + 
                       $attributes['cleaning_fee'] + 
                       $attributes['service_fee'] + 
                       $attributes['taxes'];
            },
            'status' => $this->faker->randomElement([
                VacationRentalBooking::STATUS_PENDING,
                VacationRentalBooking::STATUS_CONFIRMED,
                VacationRentalBooking::STATUS_CANCELLED,
                VacationRentalBooking::STATUS_COMPLETED,
            ]),
            'payment_status' => $this->faker->randomElement([
                VacationRentalBooking::PAYMENT_PENDING,
                VacationRentalBooking::PAYMENT_PARTIAL,
                VacationRentalBooking::PAYMENT_PAID,
                VacationRentalBooking::PAYMENT_REFUNDED,
            ]),
            'special_requests' => $this->faker->optional()->paragraph,
            'internal_notes' => $this->faker->optional()->sentence,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VacationRentalBooking::STATUS_PENDING,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VacationRentalBooking::STATUS_CANCELLED,
            'cancellation_reason' => $this->faker->sentence,
            'cancelled_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VacationRentalBooking::STATUS_COMPLETED,
            'check_out_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
            'check_in_date' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    public function active(): static
    {
        $checkInDate = $this->faker->dateTimeBetween('-7 days', 'now');
        $checkOutDate = $this->faker->dateTimeBetween('now', '+7 days');
        
        return $this->state(fn (array $attributes) => [
            'status' => VacationRentalBooking::STATUS_CONFIRMED,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
        ]);
    }
}
