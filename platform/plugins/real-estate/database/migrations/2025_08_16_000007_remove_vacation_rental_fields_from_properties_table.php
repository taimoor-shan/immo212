<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('re_properties', function (Blueprint $table) {
            // Drop the vacation rental index first
            $table->dropIndex('idx_vacation_rental_filters');
            
            // Remove vacation rental specific fields
            $table->dropColumn([
                'check_in_time',
                'check_out_time',
                'minimum_stay',
                'maximum_stay',
                'maximum_guests',
                'cleaning_fee',
                'security_deposit',
                'house_rules',
                'cancellation_policy',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('re_properties', function (Blueprint $table) {
            // Add vacation rental specific fields back
            $table->time('check_in_time')->nullable()->after('period');
            $table->time('check_out_time')->nullable()->after('check_in_time');
            $table->integer('minimum_stay')->nullable()->after('check_out_time')->comment('Minimum stay in nights');
            $table->integer('maximum_stay')->nullable()->after('minimum_stay')->comment('Maximum stay in nights, 0 = no limit');
            $table->integer('maximum_guests')->nullable()->after('maximum_stay');
            $table->decimal('cleaning_fee', 15, 2)->nullable()->after('maximum_guests');
            $table->decimal('security_deposit', 15, 2)->nullable()->after('cleaning_fee');
            $table->text('house_rules')->nullable()->after('security_deposit');
            $table->string('cancellation_policy', 50)->nullable()->after('house_rules');
            
            // Recreate the index for vacation rental queries
            $table->index(['type', 'minimum_stay', 'maximum_guests'], 'idx_vacation_rental_filters');
        });
    }
};
