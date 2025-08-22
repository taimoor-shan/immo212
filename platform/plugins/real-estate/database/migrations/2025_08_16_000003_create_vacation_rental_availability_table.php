<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('re_vacation_rental_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacation_rental_id')->constrained('re_vacation_rentals')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['available', 'blocked', 'booked', 'maintenance'])->default('available');
            $table->decimal('price_per_night', 15, 2)->nullable();
            $table->integer('minimum_stay')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['vacation_rental_id', 'date'], 'vacation_rental_availability_unique');
            $table->index(['vacation_rental_id', 'status']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_vacation_rental_availability');
    }
};
