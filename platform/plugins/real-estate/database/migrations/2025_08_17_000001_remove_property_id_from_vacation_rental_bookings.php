<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            // Drop the property_id foreign key constraint and column first
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');

            // Drop the existing foreign key constraint on vacation_rental_id
            $table->dropForeign(['vacation_rental_id']);

            // Make vacation_rental_id required (remove nullable) and add back foreign key
            $table->unsignedBigInteger('vacation_rental_id')->nullable(false)->change();
            $table->foreign('vacation_rental_id')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            // Add back property_id column
            $table->foreignId('property_id')->nullable()->after('booking_number')->constrained('re_properties')->nullOnDelete();

            // Make vacation_rental_id nullable again
            $table->foreignId('vacation_rental_id')->nullable()->change();
        });
    }
};
