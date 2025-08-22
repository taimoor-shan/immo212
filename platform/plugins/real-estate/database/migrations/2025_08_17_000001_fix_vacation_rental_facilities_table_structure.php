<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, check if the table exists and has data
        if (Schema::hasTable('re_vacation_rental_facilities_distances')) {
            // Drop existing foreign keys and indexes
            Schema::table('re_vacation_rental_facilities_distances', function (Blueprint $table) {
                // Drop foreign keys
                $table->dropForeign('vr_facilities_vr_id_fk');
                $table->dropForeign('vr_facilities_facility_id_fk');
                
                // Drop indexes
                $table->dropIndex('vr_facilities_vr_facility_idx');
                $table->dropIndex('vr_facilities_ref_type_idx');
            });
            
            // Rename vacation_rental_id to reference_id to match polymorphic pattern
            Schema::table('re_vacation_rental_facilities_distances', function (Blueprint $table) {
                $table->renameColumn('vacation_rental_id', 'reference_id');
            });
            
            // Add new foreign keys and indexes with correct polymorphic structure
            Schema::table('re_vacation_rental_facilities_distances', function (Blueprint $table) {
                // Add foreign key for facility_id
                $table->foreign('facility_id')->references('id')->on('re_facilities')->onDelete('cascade');
                
                // Add indexes for polymorphic relationship
                $table->index(['reference_id', 'reference_type'], 'vr_facilities_reference_idx');
                $table->index('facility_id', 'vr_facilities_facility_idx');
                $table->index('reference_type', 'vr_facilities_ref_type_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('re_vacation_rental_facilities_distances')) {
            // Drop the new foreign keys and indexes
            Schema::table('re_vacation_rental_facilities_distances', function (Blueprint $table) {
                $table->dropForeign(['facility_id']);
                $table->dropIndex('vr_facilities_reference_idx');
                $table->dropIndex('vr_facilities_facility_idx');
                $table->dropIndex('vr_facilities_ref_type_idx');
            });
            
            // Rename reference_id back to vacation_rental_id
            Schema::table('re_vacation_rental_facilities_distances', function (Blueprint $table) {
                $table->renameColumn('reference_id', 'vacation_rental_id');
            });
            
            // Restore original foreign keys and indexes
            Schema::table('re_vacation_rental_facilities_distances', function (Blueprint $table) {
                $table->foreign('vacation_rental_id', 'vr_facilities_vr_id_fk')->references('id')->on('re_vacation_rentals')->onDelete('cascade');
                $table->foreign('facility_id', 'vr_facilities_facility_id_fk')->references('id')->on('re_facilities')->onDelete('cascade');
                
                $table->index(['vacation_rental_id', 'facility_id'], 'vr_facilities_vr_facility_idx');
                $table->index('reference_type', 'vr_facilities_ref_type_idx');
            });
        }
    }
};
