<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('re_property_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->date('date');
            $table->enum('status', ['available', 'blocked', 'booked', 'maintenance'])->default('available');
            $table->decimal('price_per_night', 15, 2)->nullable()->comment('Override price for this specific date');
            $table->integer('minimum_stay')->nullable()->comment('Override minimum stay for this date');
            $table->text('notes')->nullable()->comment('Internal notes for this date');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('property_id')->references('id')->on('re_properties')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate entries for same property and date
            $table->unique(['property_id', 'date'], 'unique_property_date');
            
            // Indexes for performance
            $table->index(['property_id', 'date'], 'idx_property_date');
            $table->index(['property_id', 'status', 'date'], 'idx_property_status_date');
            $table->index(['date', 'status'], 'idx_date_status');
            $table->index(['property_id', 'date', 'status'], 'idx_availability_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_property_availability');
    }
};
