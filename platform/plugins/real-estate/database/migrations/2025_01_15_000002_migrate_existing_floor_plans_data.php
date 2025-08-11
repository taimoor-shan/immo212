<?php

use Illuminate\Database\Migrations\Migration;
use Botble\RealEstate\Models\Property;

return new class extends Migration {
    public function up(): void
    {
        // Migrate existing floor_plans data to new simplified structure
        Property::whereNotNull('floor_plans')->chunk(100, function ($properties) {
            foreach ($properties as $property) {
                $floorPlans = $property->floor_plans;
                
                if (is_array($floorPlans) && !empty($floorPlans)) {
                    // Take the first floor plan as the primary one
                    $firstPlan = $floorPlans[0];
                    
                    // Set floor name from the first plan's name
                    if (!empty($firstPlan['name'])) {
                        $property->floor_name = $firstPlan['name'];
                    }
                    
                    // Set floor plan image from the first plan's image
                    if (!empty($firstPlan['image'])) {
                        $property->floor_plan_image = $firstPlan['image'];
                    }
                    
                    $property->save();
                }
            }
        });
    }

    public function down(): void
    {
        // Clear the new fields
        Property::query()->update([
            'floor_name' => null,
            'floor_plan_image' => null,
            'floor_plan_document' => null,
        ]);
    }
};
