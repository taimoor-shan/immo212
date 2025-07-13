<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('re_vacation_rental_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique()->comment('Unique booking reference number');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('account_id')->nullable()->comment('Registered user account');
            
            // Guest information
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone')->nullable();
            $table->text('guest_address')->nullable();
            
            // Booking details
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('nights_count');
            $table->integer('guests_count');
            $table->integer('adults_count')->default(1);
            $table->integer('children_count')->default(0);
            
            // Pricing breakdown
            $table->decimal('base_price_per_night', 15, 2);
            $table->decimal('total_nights_cost', 15, 2);
            $table->decimal('cleaning_fee', 15, 2)->default(0);
            $table->decimal('security_deposit', 15, 2)->default(0);
            $table->decimal('service_fee', 15, 2)->default(0);
            $table->decimal('taxes', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            
            // Booking status and management
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Communication
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('property_id')->references('id')->on('re_properties')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('re_accounts')->onDelete('set null');
            
            // Indexes for performance
            $table->index(['property_id', 'status'], 'idx_property_status');
            $table->index(['property_id', 'check_in_date', 'check_out_date'], 'idx_property_dates');
            $table->index(['check_in_date', 'check_out_date'], 'idx_booking_dates');
            $table->index(['status', 'check_in_date'], 'idx_status_checkin');
            $table->index(['guest_email'], 'idx_guest_email');
            $table->index(['booking_number'], 'idx_booking_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_vacation_rental_bookings');
    }
};
