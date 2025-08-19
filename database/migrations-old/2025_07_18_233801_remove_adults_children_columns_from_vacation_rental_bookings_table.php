<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            $table->dropColumn(['adults_count', 'children_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('re_vacation_rental_bookings', function (Blueprint $table) {
            $table->integer('adults_count')->default(1);
            $table->integer('children_count')->default(0);
        });
    }
};
