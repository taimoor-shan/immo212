<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create vacation rental categories pivot table
        if (!Schema::hasTable('re_vacation_rental_categories')) {
            Schema::create('re_vacation_rental_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vacation_rental_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->foreign('vacation_rental_id')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('re_categories')->onDelete('cascade');
            
            $table->unique(['vacation_rental_id', 'category_id'], 'unique_vacation_rental_category');
            });
        }

        // Create vacation rental features pivot table
        if (!Schema::hasTable('re_vacation_rental_features')) {
            Schema::create('re_vacation_rental_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vacation_rental_id');
            $table->unsignedBigInteger('feature_id');
            $table->timestamps();

            $table->foreign('vacation_rental_id')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
            $table->foreign('feature_id')->references('id')->on('re_features')->onDelete('cascade');
            
            $table->unique(['vacation_rental_id', 'feature_id'], 'unique_vacation_rental_feature');
            });
        }

        // Create vacation rental calendar events table
        if (!Schema::hasTable('re_vacation_rental_calendar_events')) {
            Schema::create('re_vacation_rental_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vacation_rental_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('event_type', ['booking', 'blocked', 'maintenance', 'custom'])->default('custom');
            $table->string('color', 7)->default('#007bff');
            $table->timestamps();

            $table->foreign('vacation_rental_id')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('re_vacation_rental_bookings')->onDelete('set null');
            
            $table->index(['vacation_rental_id', 'start_date', 'end_date'], 'idx_vr_calendar_dates');
            $table->index(['event_type']);
            });
        }

        // Update property calendar events to support vacation rentals
        if (Schema::hasTable('re_property_calendar_events')) {
            Schema::table('re_property_calendar_events', function (Blueprint $table) {
                // Add vacation_rental_id column for backward compatibility
                $table->unsignedBigInteger('vacation_rental_id')->nullable()->after('property_id');
                $table->foreign('vacation_rental_id')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // Drop vacation rental pivot tables
        Schema::dropIfExists('re_vacation_rental_calendar_events');
        Schema::dropIfExists('re_vacation_rental_features');
        Schema::dropIfExists('re_vacation_rental_categories');

        // Remove vacation_rental_id from property calendar events
        if (Schema::hasTable('re_property_calendar_events')) {
            Schema::table('re_property_calendar_events', function (Blueprint $table) {
                $table->dropForeign(['vacation_rental_id']);
                $table->dropColumn('vacation_rental_id');
            });
        }
    }
};
