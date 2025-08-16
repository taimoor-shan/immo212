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
            
            // Basic information
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('location')->nullable();
            $table->json('images')->nullable();
            
            // Pricing
            $table->decimal('price', 15, 2)->nullable()->comment('Price per night');
            $table->unsignedBigInteger('currency_id')->nullable();
            
            // Location
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable(); 
            $table->unsignedBigInteger('country_id')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Vacation rental specific fields
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->integer('minimum_stay')->nullable();
            $table->integer('maximum_stay')->nullable();
            $table->integer('maximum_guests')->nullable();
            $table->decimal('cleaning_fee', 15, 2)->default(0);
            $table->decimal('security_deposit', 15, 2)->default(0);
            $table->text('house_rules')->nullable();
            $table->text('cancellation_policy')->nullable();
            
            // Status and management
            $table->string('status', 60)->default('published');
            $table->string('moderation_status', 60)->default('approved');
            $table->tinyInteger('is_featured')->default(0);
            $table->integer('featured_priority')->default(0);
            
            // Ownership
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('author_type')->nullable();
            
            // Dates
            $table->datetime('expire_date')->nullable();
            $table->tinyInteger('auto_renew')->default(0);
            
            // Additional fields
            $table->string('unique_id')->unique()->nullable();
            $table->text('private_notes')->nullable();
            $table->text('reject_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'moderation_status']);
            $table->index(['author_id', 'author_type']);
            $table->index(['city_id', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index(['price', 'status']);
            
            // Foreign keys (will be added if plugins exist)
            // These will be handled by the plugin's service provider
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_vacation_rentals');
    }
};
