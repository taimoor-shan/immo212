<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration {
    public function up(): void
    {
        // Set never_expired to true for all existing projects that don't have an expire_date
        // This ensures existing projects remain visible
        DB::table('re_projects')
            ->whereNull('expire_date')
            ->update([
                'never_expired' => true,
                'expire_date' => Carbon::now()->addDays(45), // Set a default expiry date
            ]);
    }

    public function down(): void
    {
        // This migration is not reversible as we don't know the original state
    }
};
