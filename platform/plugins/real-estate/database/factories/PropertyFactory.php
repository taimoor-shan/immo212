<?php

namespace Botble\RealEstate\Database\Factories;

use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'unique_id' => strtoupper(Str::random(6)),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(3, true),
            'location' => $this->faker->address,
            'images' => [],
            'number_bedroom' => $this->faker->numberBetween(1, 5),
            'number_bathroom' => $this->faker->numberBetween(1, 3),
            'number_floor' => $this->faker->numberBetween(1, 3),
            'square' => $this->faker->numberBetween(50, 500),
            'price' => $this->faker->randomFloat(2, 50000, 1000000),
            'status' => $this->faker->randomElement([PropertyStatusEnum::SELLING, PropertyStatusEnum::RENTING]),
            'type' => $this->faker->randomElement([PropertyTypeEnum::SALE, PropertyTypeEnum::RENT]),
            'is_featured' => $this->faker->boolean(20),
            'moderation_status' => ModerationStatusEnum::APPROVED,
            'author_id' => 1, // Default to admin user
            'author_type' => Account::class,
            'expire_date' => Carbon::now()->addDays($this->faker->numberBetween(30, 365)),
            'never_expired' => false,
            'latitude' => $this->faker->latitude(40, 45),
            'longitude' => $this->faker->longitude(-75, -70),
            'views' => $this->faker->numberBetween(0, 1000),
            // Vacation rental specific fields
            'check_in_time' => null,
            'check_out_time' => null,
            'minimum_stay' => null,
            'maximum_stay' => null,
            'maximum_guests' => null,
            'cleaning_fee' => null,
            'security_deposit' => null,
            'house_rules' => null,
            'cancellation_policy' => null,
        ];
    }



    public function forSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PropertyTypeEnum::SALE,
            'status' => PropertyStatusEnum::SELLING,
        ]);
    }

    public function forRent(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PropertyTypeEnum::RENT,
            'status' => PropertyStatusEnum::RENTING,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'moderation_status' => ModerationStatusEnum::APPROVED,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'moderation_status' => ModerationStatusEnum::PENDING,
        ]);
    }
}
