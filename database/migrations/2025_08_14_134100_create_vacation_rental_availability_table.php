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
            $table->unsignedBigInteger('vacation_rental_id');
            $table->date('date');
            $table->enum('status', ['available', 'booked', 'blocked', 'maintenance'])->default('available');
            $table->decimal('price', 15, 2)->nullable()->comment('Override price for specific date');
            $table->text('notes')->nullable();
            $table->text('reason')->nullable()->comment('Reason for blocking/maintenance');
            $table->timestamps();

            // Foreign key
            $table->foreign('vacation_rental_id')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
            
            // Indexes
            $table->unique(['vacation_rental_id', 'date'], 'idx_vacation_rental_date_unique');
            $table->index(['vacation_rental_id', 'status'], 'idx_vacation_rental_status');
            $table->index(['date', 'status'], 'idx_date_status');
            $table->index(['status'], 'idx_availability_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_vacation_rental_availability');
    }
};
