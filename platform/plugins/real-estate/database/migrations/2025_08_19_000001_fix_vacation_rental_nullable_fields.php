<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('re_vacation_rentals', function (Blueprint $table) {
            // Make these fields nullable to match the properties table pattern
            // This allows users to leave these fields empty during creation
            $table->integer('number_bedroom')->nullable()->default(null)->change();
            $table->integer('number_bathroom')->nullable()->default(null)->change();
            $table->integer('number_floor')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('re_vacation_rentals', function (Blueprint $table) {
            // Revert back to the original schema with default values
            $table->integer('number_bedroom')->default(0)->change();
            $table->integer('number_bathroom')->default(0)->change();
            $table->integer('number_floor')->default(0)->change();
        });
    }
};
