<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add the missing status column to the vacation rentals table.
     * This column was defined in the original migration but seems to be missing from the actual table.
     */
    public function up(): void
    {
        Schema::table('re_vacation_rentals', function (Blueprint $table) {
            // Check if status column exists, if not add it
            if (! Schema::hasColumn('re_vacation_rentals', 'status')) {
                $table->string('status', 60)->default('selling')->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('re_vacation_rentals', function (Blueprint $table) {
            if (Schema::hasColumn('re_vacation_rentals', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
