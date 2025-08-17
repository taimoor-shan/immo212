<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vacation rental categories pivot table
        Schema::create('re_vacation_rental_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacation_rental_id')->constrained('re_vacation_rentals')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('re_categories')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['vacation_rental_id', 'category_id'], 'vacation_rental_category_unique');
        });

        // Vacation rental features pivot table
        Schema::create('re_vacation_rental_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacation_rental_id')->constrained('re_vacation_rentals')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('re_features')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['vacation_rental_id', 'feature_id'], 'vacation_rental_feature_unique');
        });

        // Vacation rental facilities distances table (similar to properties)
        Schema::create('re_vacation_rental_facilities_distances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vacation_rental_id');
            $table->unsignedBigInteger('facility_id');
            $table->string('reference_type')->default('Botble\RealEstate\Models\VacationRental');
            $table->string('distance')->nullable();
            $table->timestamps();

            $table->foreign('vacation_rental_id', 'vr_facilities_vr_id_fk')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
            $table->foreign('facility_id', 'vr_facilities_facility_id_fk')->references('id')->on('re_facilities')->onDelete('cascade');

            $table->index(['vacation_rental_id', 'facility_id'], 'vr_facilities_vr_facility_idx');
            $table->index('reference_type', 'vr_facilities_ref_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_vacation_rental_facilities_distances');
        Schema::dropIfExists('re_vacation_rental_features');
        Schema::dropIfExists('re_vacation_rental_categories');
    }
};
