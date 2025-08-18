<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            // First, make vacation_rental_id required (remove nullable)
            $table->foreignId('vacation_rental_id')->nullable(false)->change();
            
            // Drop the property_id foreign key constraint and column
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
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
