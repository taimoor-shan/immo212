<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('re_vacation_rental_availability_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacation_rental_id')->constrained('re_vacation_rentals')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('rule_type', ['seasonal', 'weekly', 'special_event', 'minimum_stay', 'maximum_stay'])->default('seasonal');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('days_of_week')->nullable(); // For weekly rules: [1,2,3,4,5,6,7]
            $table->decimal('price_adjustment', 8, 2)->nullable(); // Percentage or fixed amount
            $table->enum('price_adjustment_type', ['percentage', 'fixed'])->default('percentage');
            $table->integer('minimum_stay_override')->nullable();
            $table->integer('maximum_stay_override')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Higher priority rules override lower ones
            $table->timestamps();
            
            $table->index(['vacation_rental_id', 'rule_type'], 'vr_rules_vr_type_idx');
            $table->index(['start_date', 'end_date'], 'vr_rules_dates_idx');
            $table->index(['is_active', 'priority'], 'vr_rules_active_priority_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_vacation_rental_availability_rules');
    }
};
