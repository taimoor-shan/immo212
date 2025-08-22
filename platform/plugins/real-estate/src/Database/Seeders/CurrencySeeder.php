<?php

namespace Botble\RealEstate\Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Currency;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends BaseSeeder
{
    public function run(): void
    {
        // Use delete instead of truncate to avoid foreign key constraint issues
        Currency::query()->delete();

        // Reset auto increment
        DB::statement('ALTER TABLE re_currencies AUTO_INCREMENT = 1;');

        $currencies = [
            [
                'title' => 'USD',
                'symbol' => '$',
                'is_prefix_symbol' => true,
                'order' => 0,
                'decimals' => 0,
                'is_default' => 1,
                'exchange_rate' => 1,
            ],
            [
                'title' => 'EUR',
                'symbol' => '€',
                'is_prefix_symbol' => false,
                'order' => 1,
                'decimals' => 0,
                'is_default' => 0,
                'exchange_rate' => 0.91,
            ],
            [
                'title' => 'VND',
                'symbol' => '₫',
                'is_prefix_symbol' => false,
                'order' => 2,
                'decimals' => 0,
                'is_default' => 0,
                'exchange_rate' => 23717.5,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::query()->create($currency);
        }
    }
}
