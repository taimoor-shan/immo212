<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('re_property_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('event_type', ['booking', 'blocked', 'maintenance', 'personal_use', 'cleaning'])->default('blocked');
            $table->string('color', 7)->default('#ff6b35')->comment('Hex color for calendar display');
            $table->unsignedBigInteger('booking_id')->nullable()->comment('Link to booking if event_type is booking');
            $table->boolean('is_all_day')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_pattern')->nullable()->comment('Recurring pattern configuration');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('property_id')->references('id')->on('re_properties')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('re_vacation_rental_bookings')->onDelete('cascade');
            
            // Indexes for performance
            $table->index(['property_id', 'start_date', 'end_date'], 'idx_property_date_range');
            $table->index(['property_id', 'event_type'], 'idx_property_event_type');
            $table->index(['start_date', 'end_date'], 'idx_date_range');
            $table->index(['booking_id'], 'idx_booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_property_calendar_events');
    }
};
