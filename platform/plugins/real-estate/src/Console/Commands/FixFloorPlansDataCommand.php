<?php

namespace Botble\RealEstate\Console\Commands;

use Botble\RealEstate\Models\Property;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixFloorPlansDataCommand extends Command
{
    protected $signature = 'real-estate:fix-floor-plans-data';

    protected $description = 'Fix floor plans data consistency for conditional floor plans system';

    public function handle(): int
    {
        $this->info('Starting floor plans data consistency check...');

        $properties = Property::query()
            ->whereNotNull('number_floor')
            ->get();

        $fixedCount = 0;
        $totalCount = $properties->count();

        $this->info("Found {$totalCount} properties to check.");

        foreach ($properties as $property) {
            $wasFixed = $this->fixPropertyFloorPlansData($property);
            if ($wasFixed) {
                $fixedCount++;
            }
        }

        $this->info("Processed {$totalCount} properties.");
        $this->info("Fixed {$fixedCount} properties with data inconsistencies.");

        return self::SUCCESS;
    }

    private function fixPropertyFloorPlansData(Property $property): bool
    {
        $wasFixed = false;
        $changes = [];

        // Case 1: Single floor property (number_floor = 1)
        if ($property->number_floor === 1) {
            // If it has multiple floor plans data but should be single floor
            if ($property->floor_plans && is_array($property->floor_plans) && count($property->floor_plans) > 0) {
                $firstFloorPlan = $property->floor_plans[0];
                
                // Extract data from first floor plan
                if (is_array($firstFloorPlan)) {
                    $floorPlanData = collect($firstFloorPlan)->pluck('value', 'key')->toArray();
                    
                    // Migrate to single floor plan fields
                    if (!$property->floor_name && isset($floorPlanData['name'])) {
                        $property->floor_name = $floorPlanData['name'];
                        $changes[] = 'floor_name';
                    }
                    
                    if (!$property->floor_plan_image && isset($floorPlanData['image'])) {
                        $property->floor_plan_image = $floorPlanData['image'];
                        $changes[] = 'floor_plan_image';
                    }
                    
                    // Clear the multiple floor plans data
                    $property->floor_plans = null;
                    $changes[] = 'cleared floor_plans';
                    
                    $wasFixed = true;
                }
            }
        }
        
        // Case 2: Multi-floor property (number_floor > 1)
        elseif ($property->number_floor > 1) {
            // If it has single floor plan data but should be multi-floor
            if (($property->floor_name || $property->floor_plan_image || $property->floor_plan_document) 
                && (!$property->floor_plans || empty($property->floor_plans))) {
                
                // Migrate single floor data to multi-floor format
                $floorPlansData = [];
                
                for ($i = 1; $i <= $property->number_floor; $i++) {
                    $floorName = $i === 1 && $property->floor_name 
                        ? $property->floor_name 
                        : $this->generateFloorName($i);
                    
                    $floorData = [
                        ['key' => 'name', 'value' => $floorName],
                        ['key' => 'description', 'value' => ''],
                        ['key' => 'bedrooms', 'value' => $i === 1 ? ($property->number_bedroom ?? 0) : 0],
                        ['key' => 'bathrooms', 'value' => $i === 1 ? ($property->number_bathroom ?? 0) : 0],
                    ];
                    
                    // Add image only to first floor if available
                    if ($i === 1 && $property->floor_plan_image) {
                        $floorData[] = ['key' => 'image', 'value' => $property->floor_plan_image];
                    } else {
                        $floorData[] = ['key' => 'image', 'value' => ''];
                    }
                    
                    $floorPlansData[] = $floorData;
                }
                
                $property->floor_plans = $floorPlansData;
                $changes[] = 'migrated to floor_plans';
                
                // Clear single floor data
                $property->floor_name = null;
                $property->floor_plan_image = null;
                $property->floor_plan_document = null;
                $changes[] = 'cleared single floor fields';
                
                $wasFixed = true;
            }
        }

        if ($wasFixed) {
            $property->save();
            $this->line("Fixed property ID {$property->id}: " . implode(', ', $changes));
        }

        return $wasFixed;
    }

    private function generateFloorName(int $floorNumber): string
    {
        if ($floorNumber === 1) {
            return 'Ground Floor';
        } elseif ($floorNumber === 2) {
            return '1st Floor';
        } elseif ($floorNumber === 3) {
            return '2nd Floor';
        } elseif ($floorNumber === 4) {
            return '3rd Floor';
        } else {
            return ($floorNumber - 1) . 'th Floor';
        }
    }
}
