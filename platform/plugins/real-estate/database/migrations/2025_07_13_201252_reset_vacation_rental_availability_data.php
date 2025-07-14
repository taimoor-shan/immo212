<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Reset all vacation rental availability data to start fresh with individual dates system
     */
    public function up(): void
    {
        \Log::info('Starting reset of vacation rental availability data');

        // Get all vacation rental property IDs
        $vacationRentalIds = \DB::table('re_properties')
            ->where('type', 'vacation_rental')
            ->pluck('id')
            ->toArray();

        \Log::info('Found vacation rental properties', ['count' => count($vacationRentalIds)]);

        if (!empty($vacationRentalIds)) {
            // Delete all calendar events for vacation rentals
            $deletedEvents = \DB::table('re_property_calendar_events')
                ->whereIn('property_id', $vacationRentalIds)
                ->delete();
            \Log::info('Deleted calendar events', ['count' => $deletedEvents]);

            // Delete all availability records for vacation rentals
            $deletedAvailability = \DB::table('re_property_availability')
                ->whereIn('property_id', $vacationRentalIds)
                ->delete();
            \Log::info('Deleted availability records', ['count' => $deletedAvailability]);
        }

        \Log::info('Reset completed - vacation rental properties now have clean availability data');
        \Log::info('You can now test the new individual dates system with fresh data');
    }

    /**
     * Reverse the migrations.
     * This migration clears data, so reversal is not applicable
     */
    public function down(): void
    {
        \Log::info('Reset migration reversal - no action needed');
        \Log::info('Data was cleared in the up() method, down() method does not restore data');
    }
};
