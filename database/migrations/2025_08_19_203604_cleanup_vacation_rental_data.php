<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clean up vacation rental related tables in the correct order
        // (child tables first, then parent tables)

        // 1. Clear property calendar events (references vacation rental bookings)
        if (Schema::hasTable('re_property_calendar_events')) {
            DB::table('re_property_calendar_events')->truncate();
        }

        // 2. Clear vacation rental bookings
        if (Schema::hasTable('re_vacation_rental_bookings')) {
            DB::table('re_vacation_rental_bookings')->truncate();
        }

        // 3. Clear vacation rental availability
        if (Schema::hasTable('re_vacation_rental_availability')) {
            DB::table('re_vacation_rental_availability')->truncate();
        }

        // 4. Clear vacation rental calendar events
        if (Schema::hasTable('re_vacation_rental_calendar_events')) {
            DB::table('re_vacation_rental_calendar_events')->truncate();
        }

        // 5. Clear vacation rental availability rules
        if (Schema::hasTable('re_vacation_rental_availability_rules')) {
            DB::table('re_vacation_rental_availability_rules')->truncate();
        }

        // 6. Clear vacation rental pivot tables
        if (Schema::hasTable('re_vacation_rental_features')) {
            DB::table('re_vacation_rental_features')->truncate();
        }

        if (Schema::hasTable('re_vacation_rental_facilities')) {
            DB::table('re_vacation_rental_facilities')->truncate();
        }

        // 7. Finally clear the main vacation rentals table
        if (Schema::hasTable('re_vacation_rentals')) {
            DB::table('re_vacation_rentals')->truncate();
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only cleans data, no structural changes to reverse
        // The down method is intentionally left empty
    }
};
