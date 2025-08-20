<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Map vacation rental statuses for core system compatibility:
     * - Keep 'draft' as 'draft' (hidden from frontend)
     * - Map 'renting' to 'published' (visible on frontend, core system compatible)
     */
    public function up(): void
    {
        // Update existing 'renting' status to 'published' for core system compatibility
        DB::table('re_vacation_rentals')
            ->where('status', 'renting')
            ->update(['status' => 'published']);
            
        // Ensure any invalid statuses are set to draft
        DB::table('re_vacation_rentals')
            ->whereNotIn('status', ['draft', 'published'])
            ->update(['status' => 'draft']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'published' back to 'renting'
        DB::table('re_vacation_rentals')
            ->where('status', 'published')
            ->update(['status' => 'renting']);
    }
};
