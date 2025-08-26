<?php

use Botble\RealEstate\Enums\CategoryTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Assign appropriate category types to existing categories.
     * This is a safe operation that maintains backward compatibility.
     */
    public function up(): void
    {
        // Define category type mappings
        $categoryMappings = [
            // Categories suitable for vacation rentals (exclude Land as it doesn't make sense)
            'Apartment' => [CategoryTypeEnum::PROPERTY, CategoryTypeEnum::PROJECT, CategoryTypeEnum::VACATION_RENTAL],
            'Villa' => [CategoryTypeEnum::PROPERTY, CategoryTypeEnum::PROJECT, CategoryTypeEnum::VACATION_RENTAL], 
            'Condo' => [CategoryTypeEnum::PROPERTY, CategoryTypeEnum::PROJECT, CategoryTypeEnum::VACATION_RENTAL],
            'House' => [CategoryTypeEnum::PROPERTY, CategoryTypeEnum::PROJECT, CategoryTypeEnum::VACATION_RENTAL],
            
            // Categories not suitable for vacation rentals
            'Land' => [CategoryTypeEnum::PROPERTY, CategoryTypeEnum::PROJECT],
            'Commercial' => [CategoryTypeEnum::PROPERTY, CategoryTypeEnum::PROJECT],
            'Office' => [CategoryTypeEnum::PROPERTY, CategoryTypeEnum::PROJECT],
            'Industrial' => [CategoryTypeEnum::PROPERTY, CategoryTypeEnum::PROJECT],
        ];

        // Update existing categories
        foreach ($categoryMappings as $categoryName => $types) {
            DB::table('re_categories')
                ->where('name', $categoryName)
                ->update([
                    'category_types' => json_encode($types)
                ]);
        }

        // For any categories not explicitly mapped, assign all types (safe default)
        DB::table('re_categories')
            ->whereNull('category_types')
            ->update([
                'category_types' => json_encode([
                    CategoryTypeEnum::PROPERTY, 
                    CategoryTypeEnum::PROJECT, 
                    CategoryTypeEnum::VACATION_RENTAL
                ])
            ]);
    }

    /**
     * Reverse the migrations.
     * 
     * Remove category types (sets them back to null)
     */
    public function down(): void
    {
        DB::table('re_categories')->update(['category_types' => null]);
    }
};
