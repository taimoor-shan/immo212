<?php

namespace Botble\RealEstate\Database\Factories;

use Botble\Base\Facades\Html;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\VacationRental;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class VacationRentalFactory extends Factory
{
    protected $model = VacationRental::class;

    public function definition(): array
    {
        $name = $this->faker->words(rand(2, 4), true);
        $description = $this->faker->sentences(rand(2, 4), true);
        
        return [
            'name' => $name,
            'description' => $description,
            'content' => Html::tag('p', $description) . Html::tag('p', $this->faker->realText(500)),
            'location' => $this->faker->address,
            'images' => json_encode([
                'properties/' . $this->faker->numberBetween(1, 20) . '.jpg',
                'properties/' . $this->faker->numberBetween(1, 20) . '.jpg',
                'properties/' . $this->faker->numberBetween(1, 20) . '.jpg',
            ]),
            'number_bedroom' => $this->faker->numberBetween(1, 5),
            'number_bathroom' => $this->faker->numberBetween(1, 4),
            'number_floor' => $this->faker->numberBetween(1, 3),
            'square' => $this->faker->numberBetween(50, 500),
            'price' => $this->faker->numberBetween(50, 500), // Price per night
            'status' => PropertyStatusEnum::SELLING,
            'moderation_status' => ModerationStatusEnum::APPROVED,
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'featured_priority' => $this->faker->optional(0.2)->numberBetween(1, 10),
            'currency_id' => Currency::query()->inRandomOrder()->first()?->id,
            'author_id' => Account::query()->inRandomOrder()->first()?->id,
            'author_type' => Account::class,
            'expire_date' => $this->faker->optional(0.3)->dateTimeBetween('now', '+1 year'),
            'auto_renew' => $this->faker->boolean(30),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'unique_id' => $this->faker->unique()->uuid,
            'private_notes' => $this->faker->optional(0.3)->sentence,
            
            // Vacation rental specific fields
            'check_in_time' => $this->faker->time('H:i', '16:00'),
            'check_out_time' => $this->faker->time('H:i', '11:00'),
            'minimum_stay' => $this->faker->numberBetween(1, 7),
            'maximum_stay' => $this->faker->optional(0.7)->numberBetween(7, 30),
            'maximum_guests' => $this->faker->numberBetween(2, 12),
            'cleaning_fee' => $this->faker->optional(0.8)->numberBetween(20, 100),
            'security_deposit' => $this->faker->optional(0.6)->numberBetween(100, 500),
            'house_rules' => $this->faker->optional(0.7)->sentences(3, true),
            'cancellation_policy' => $this->faker->randomElement(['flexible', 'moderate', 'strict', 'super_strict']),
            
            'views' => $this->faker->numberBetween(0, 1000),
        ];
    }

    public function featured(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
                'featured_priority' => $this->faker->numberBetween(1, 10),
            ];
        });
    }

    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'moderation_status' => ModerationStatusEnum::PENDING,
            ];
        });
    }

    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'moderation_status' => ModerationStatusEnum::REJECTED,
                'reject_reason' => $this->faker->sentence,
            ];
        });
    }

    public function withAuthor(Account $account): static
    {
        return $this->state(function (array $attributes) use ($account) {
            return [
                'author_id' => $account->id,
                'author_type' => Account::class,
            ];
        });
    }

    public function withCategory(Category $category): static
    {
        return $this->afterCreating(function (VacationRental $vacationRental) use ($category) {
            $vacationRental->categories()->attach($category);
        });
    }

    public function withCategories(array $categories): static
    {
        return $this->afterCreating(function (VacationRental $vacationRental) use ($categories) {
            $vacationRental->categories()->attach($categories);
        });
    }

    public function luxury(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->numberBetween(200, 1000),
                'number_bedroom' => $this->faker->numberBetween(3, 6),
                'number_bathroom' => $this->faker->numberBetween(2, 5),
                'square' => $this->faker->numberBetween(150, 800),
                'maximum_guests' => $this->faker->numberBetween(6, 16),
                'cleaning_fee' => $this->faker->numberBetween(50, 200),
                'security_deposit' => $this->faker->numberBetween(300, 1000),
                'is_featured' => true,
                'featured_priority' => $this->faker->numberBetween(8, 10),
            ];
        });
    }

    public function budget(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->numberBetween(25, 100),
                'number_bedroom' => $this->faker->numberBetween(1, 2),
                'number_bathroom' => $this->faker->numberBetween(1, 2),
                'square' => $this->faker->numberBetween(30, 100),
                'maximum_guests' => $this->faker->numberBetween(2, 4),
                'cleaning_fee' => $this->faker->optional(0.5)->numberBetween(10, 30),
                'security_deposit' => $this->faker->optional(0.4)->numberBetween(50, 150),
            ];
        });
    }
}
