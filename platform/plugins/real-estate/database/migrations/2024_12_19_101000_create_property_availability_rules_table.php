<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('re_property_availability_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('name')->comment('Rule name (e.g., "Summer Season", "Holiday Pricing")');
            $table->enum('type', ['seasonal_pricing', 'blocked_period', 'minimum_stay', 'special_rate'])->default('seasonal_pricing');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('days_of_week')->nullable()->comment('Array of days [0=Sunday, 1=Monday, etc.] or null for all days');
            $table->decimal('price_modifier', 8, 2)->nullable()->comment('Multiplier for base price (1.5 = 150% of base price)');
            $table->decimal('fixed_price', 15, 2)->nullable()->comment('Fixed price override');
            $table->integer('minimum_stay_override')->nullable()->comment('Override minimum stay for this period');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0)->comment('Higher priority rules override lower priority ones');
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('property_id')->references('id')->on('re_properties')->onDelete('cascade');
            
            // Indexes for performance
            $table->index(['property_id', 'is_active'], 'idx_property_active');
            $table->index(['property_id', 'start_date', 'end_date'], 'idx_property_date_range');
            $table->index(['type', 'is_active'], 'idx_type_active');
            $table->index(['property_id', 'type', 'is_active', 'priority'], 'idx_rule_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_property_availability_rules');
    }
};
