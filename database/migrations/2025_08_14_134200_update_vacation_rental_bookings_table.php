<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            // Add new vacation_rental_id column
            $table->unsignedBigInteger('vacation_rental_id')->nullable()->after('property_id');
            
            // Add foreign key for vacation_rental_id
            $table->foreign('vacation_rental_id')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
            
            // Add index for performance
            $table->index(['vacation_rental_id', 'status'], 'idx_vacation_rental_status');
            $table->index(['vacation_rental_id', 'check_in_date', 'check_out_date'], 'idx_vacation_rental_dates');
        });
    }

    public function down(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_vacation_rental_status');
            $table->dropIndex('idx_vacation_rental_dates');
            
            // Drop foreign key and column
            $table->dropForeign(['vacation_rental_id']);
            $table->dropColumn('vacation_rental_id');
        });
    }
};
