<?php

namespace Botble\RealEstate\Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Facades\DB;

class VacationRentalSeeder extends BaseSeeder
{
    public function run(): void
    {
        // Disable foreign key checks to avoid constraint violations
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate vacation rental related tables first to ensure clean data
        $vacationRentalTables = [
            're_vacation_rental_availability_rules',
            're_vacation_rental_availability',
            're_vacation_rental_calendar_events',
            're_vacation_rental_bookings',
            're_vacation_rental_facilities_distances',
            're_vacation_rental_features',
            're_vacation_rental_categories',
            're_vacation_rentals'
        ];

        foreach ($vacationRentalTables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        $this->command->info('Vacation rental tables truncated successfully.');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('VacationRentalSeeder completed successfully.');
    }
}
