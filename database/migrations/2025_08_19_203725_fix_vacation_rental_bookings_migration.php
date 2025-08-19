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
        // Check what foreign keys exist
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 're_vacation_rental_bookings'
            AND CONSTRAINT_NAME LIKE '%_foreign'
        ");

        $existingForeignKeys = collect($foreignKeys)->pluck('CONSTRAINT_NAME')->toArray();

        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) use ($existingForeignKeys) {
            // Check if property_id column exists and drop it if it does
            if (Schema::hasColumn('re_vacation_rental_bookings', 'property_id')) {
                // Drop foreign key if it exists
                if (in_array('re_vacation_rental_bookings_property_id_foreign', $existingForeignKeys)) {
                    $table->dropForeign(['property_id']);
                }
                $table->dropColumn('property_id');
            }

            // Check if vacation_rental_id foreign key exists and drop it
            if (in_array('re_vacation_rental_bookings_vacation_rental_id_foreign', $existingForeignKeys)) {
                $table->dropForeign(['vacation_rental_id']);
            }

            // Make vacation_rental_id required (remove nullable) and add back foreign key
            if (Schema::hasColumn('re_vacation_rental_bookings', 'vacation_rental_id')) {
                $table->unsignedBigInteger('vacation_rental_id')->nullable(false)->change();
                $table->foreign('vacation_rental_id')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            // Add back property_id column if it doesn't exist
            if (!Schema::hasColumn('re_vacation_rental_bookings', 'property_id')) {
                $table->foreignId('property_id')->nullable()->after('booking_number')->constrained('re_properties')->nullOnDelete();
            }

            // Make vacation_rental_id nullable again
            if (Schema::hasColumn('re_vacation_rental_bookings', 'vacation_rental_id')) {
                $table->dropForeign(['vacation_rental_id']);
                $table->unsignedBigInteger('vacation_rental_id')->nullable()->change();
            }
        });
    }
};
