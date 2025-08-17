<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('re_vacation_rental_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacation_rental_id')->constrained('re_vacation_rentals')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('event_type', ['booking', 'blocked', 'maintenance', 'custom'])->default('custom');
            $table->string('color', 7)->default('#007bff');
            $table->foreignId('booking_id')->nullable()->constrained('re_vacation_rental_bookings')->nullOnDelete();
            $table->boolean('is_all_day')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_pattern')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['vacation_rental_id', 'start_date', 'end_date'], 'vr_events_vr_dates_idx');
            $table->index(['event_type', 'start_date'], 'vr_events_type_date_idx');
            $table->index('booking_id', 'vr_events_booking_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_vacation_rental_calendar_events');
    }
};
