<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add category_types column to allow filtering categories by model type.
     * This enables us to control which categories are available for properties, 
     * projects, and vacation rentals without breaking existing functionality.
     */
    public function up(): void
    {
        Schema::table('re_categories', function (Blueprint $table) {
            // Add JSON column to store applicable model types
            $table->json('category_types')->nullable()->after('is_default')->comment('JSON array of model types this category applies to (property, project, vacation_rental)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('re_categories', function (Blueprint $table) {
            $table->dropColumn('category_types');
        });
    }
};
