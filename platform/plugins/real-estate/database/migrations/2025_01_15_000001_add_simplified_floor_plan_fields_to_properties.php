<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->string('floor_name')->nullable()->after('number_floor');
            $table->string('floor_plan_image')->nullable()->after('floor_plans');
            $table->string('floor_plan_document')->nullable()->after('floor_plan_image');
        });
    }

    public function down(): void
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->dropColumn(['floor_name', 'floor_plan_image', 'floor_plan_document']);
        });
    }
};
