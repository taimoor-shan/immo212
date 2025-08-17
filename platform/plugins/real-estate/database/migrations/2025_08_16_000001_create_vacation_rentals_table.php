<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('re_vacation_rentals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('location')->nullable();
            $table->json('images')->nullable();
            
            // Basic property details
            $table->integer('number_bedroom')->default(0);
            $table->integer('number_bathroom')->default(0);
            $table->integer('number_floor')->default(0);
            $table->decimal('square', 8, 2)->nullable();
            $table->decimal('price', 15, 2)->nullable()->comment('Price per night');
            
            // Status and moderation
            $table->string('status', 60)->default('selling');
            $table->string('moderation_status', 60)->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->integer('featured_priority')->nullable();
            
            // Location and currency
            $table->foreignId('currency_id')->nullable()->constrained('re_currencies')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            
            // Author information
            $table->foreignId('author_id')->nullable()->constrained('re_accounts')->nullOnDelete();
            $table->string('author_type', 255)->default('Botble\RealEstate\Models\Account');
            
            // Dates and expiry
            $table->timestamp('expire_date')->nullable();
            $table->boolean('auto_renew')->default(false);
            
            // Coordinates
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Unique identifier
            $table->string('unique_id', 191)->nullable()->unique();
            $table->text('private_notes')->nullable();
            
            // Floor plans
            $table->json('floor_plans')->nullable();
            $table->string('floor_name')->nullable();
            $table->string('floor_plan_image')->nullable();
            $table->string('floor_plan_document')->nullable();
            
            // Moderation
            $table->text('reject_reason')->nullable();
            
            // Vacation rental specific fields
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->integer('minimum_stay')->nullable()->comment('Minimum stay in nights');
            $table->integer('maximum_stay')->nullable()->comment('Maximum stay in nights, 0 = no limit');
            $table->integer('maximum_guests')->nullable();
            $table->decimal('cleaning_fee', 15, 2)->nullable();
            $table->decimal('security_deposit', 15, 2)->nullable();
            $table->text('house_rules')->nullable();
            $table->string('cancellation_policy', 50)->nullable();
            
            // Tracking
            $table->integer('views')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'moderation_status']);
            $table->index(['is_featured', 'featured_priority']);
            $table->index(['minimum_stay', 'maximum_guests']);
            $table->index(['city_id', 'state_id', 'country_id']);
            $table->index(['author_id', 'author_type']);
            $table->index('expire_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_vacation_rentals');
    }
};
