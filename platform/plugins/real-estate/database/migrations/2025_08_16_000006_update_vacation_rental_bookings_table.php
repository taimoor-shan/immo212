<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            // Add vacation_rental_id column
            $table->foreignId('vacation_rental_id')->nullable()->after('property_id')->constrained('re_vacation_rentals')->nullOnDelete();
            
            // Add index for vacation_rental_id
            $table->index('vacation_rental_id');
        });
    }

    public function down(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            $table->dropForeign(['vacation_rental_id']);
            $table->dropIndex(['vacation_rental_id']);
            $table->dropColumn('vacation_rental_id');
        });
    }
};
