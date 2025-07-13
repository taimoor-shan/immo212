<?php

namespace Botble\RealEstate\Database\Factories;

use Botble\RealEstate\Models\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'dob' => $this->faker->date(),
            'phone' => $this->faker->phoneNumber,
            'description' => $this->faker->paragraph,
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'company' => $this->faker->company,
            'is_featured' => $this->faker->boolean(10),
            'is_public_profile' => $this->faker->boolean(80),
            'credits' => $this->faker->numberBetween(0, 100),
            'confirmed_at' => Carbon::now(),
            'approved_at' => Carbon::now(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed_at' => null,
            'email_verified_at' => null,
        ]);
    }

    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function withCredits(int $credits): static
    {
        return $this->state(fn (array $attributes) => [
            'credits' => $credits,
        ]);
    }
}
