<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove only floor plan related fields from vacation rentals table.
     * These fields are inherited from Property model but not used in vacation rental functionality.
     */
    public function up(): void
    {
        Schema::table('re_vacation_rentals', function (Blueprint $table) {
            // Remove floor plan related fields (not used in vacation rentals)
            $columnsToRemove = [];

            if (Schema::hasColumn('re_vacation_rentals', 'floor_plans')) {
                $columnsToRemove[] = 'floor_plans';
            }
            if (Schema::hasColumn('re_vacation_rentals', 'floor_name')) {
                $columnsToRemove[] = 'floor_name';
            }
            if (Schema::hasColumn('re_vacation_rentals', 'floor_plan_image')) {
                $columnsToRemove[] = 'floor_plan_image';
            }
            if (Schema::hasColumn('re_vacation_rentals', 'floor_plan_document')) {
                $columnsToRemove[] = 'floor_plan_document';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('re_vacation_rentals', function (Blueprint $table) {
            // Add back floor plan fields
            $table->json('floor_plans')->nullable();
            $table->string('floor_name')->nullable();
            $table->string('floor_plan_image')->nullable();
            $table->string('floor_plan_document')->nullable();
        });
    }
};
