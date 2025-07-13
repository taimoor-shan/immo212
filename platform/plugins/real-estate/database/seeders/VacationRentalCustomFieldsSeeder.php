<?php

namespace Botble\RealEstate\Database\Seeders;

use Botble\ACL\Models\User;
use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Enums\CustomFieldEnum;
use Botble\RealEstate\Models\CustomField;
use Botble\RealEstate\Models\CustomFieldOption;

class VacationRentalCustomFieldsSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('properties');

        $adminUser = User::query()->first();

        $vacationRentalFields = [
            [
                'name' => 'Check-in Time',
                'type' => CustomFieldEnum::DROPDOWN,
                'order' => 1,
                'is_global' => true,
                'options' => [
                    ['label' => '12:00 PM', 'value' => '12:00', 'order' => 1],
                    ['label' => '1:00 PM', 'value' => '13:00', 'order' => 2],
                    ['label' => '2:00 PM', 'value' => '14:00', 'order' => 3],
                    ['label' => '3:00 PM', 'value' => '15:00', 'order' => 4],
                    ['label' => '4:00 PM', 'value' => '16:00', 'order' => 5],
                    ['label' => '5:00 PM', 'value' => '17:00', 'order' => 6],
                ],
            ],
            [
                'name' => 'Check-out Time',
                'type' => CustomFieldEnum::DROPDOWN,
                'order' => 2,
                'is_global' => true,
                'options' => [
                    ['label' => '9:00 AM', 'value' => '09:00', 'order' => 1],
                    ['label' => '10:00 AM', 'value' => '10:00', 'order' => 2],
                    ['label' => '11:00 AM', 'value' => '11:00', 'order' => 3],
                    ['label' => '12:00 PM', 'value' => '12:00', 'order' => 4],
                ],
            ],
            [
                'name' => 'Minimum Stay (nights)',
                'type' => CustomFieldEnum::DROPDOWN,
                'order' => 3,
                'is_global' => true,
                'options' => [
                    ['label' => '1 night', 'value' => '1', 'order' => 1],
                    ['label' => '2 nights', 'value' => '2', 'order' => 2],
                    ['label' => '3 nights', 'value' => '3', 'order' => 3],
                    ['label' => '7 nights', 'value' => '7', 'order' => 4],
                    ['label' => '14 nights', 'value' => '14', 'order' => 5],
                    ['label' => '30 nights', 'value' => '30', 'order' => 6],
                ],
            ],
            [
                'name' => 'Maximum Stay (nights)',
                'type' => CustomFieldEnum::DROPDOWN,
                'order' => 4,
                'is_global' => true,
                'options' => [
                    ['label' => '7 nights', 'value' => '7', 'order' => 1],
                    ['label' => '14 nights', 'value' => '14', 'order' => 2],
                    ['label' => '30 nights', 'value' => '30', 'order' => 3],
                    ['label' => '90 nights', 'value' => '90', 'order' => 4],
                    ['label' => 'No limit', 'value' => '0', 'order' => 5],
                ],
            ],
            [
                'name' => 'Maximum Guests',
                'type' => CustomFieldEnum::DROPDOWN,
                'order' => 5,
                'is_global' => true,
                'options' => [
                    ['label' => '1 guest', 'value' => '1', 'order' => 1],
                    ['label' => '2 guests', 'value' => '2', 'order' => 2],
                    ['label' => '4 guests', 'value' => '4', 'order' => 3],
                    ['label' => '6 guests', 'value' => '6', 'order' => 4],
                    ['label' => '8 guests', 'value' => '8', 'order' => 5],
                    ['label' => '10 guests', 'value' => '10', 'order' => 6],
                    ['label' => '12+ guests', 'value' => '12', 'order' => 7],
                ],
            ],
            [
                'name' => 'Cleaning Fee',
                'type' => CustomFieldEnum::TEXT,
                'order' => 6,
                'is_global' => true,
            ],
            [
                'name' => 'Security Deposit',
                'type' => CustomFieldEnum::TEXT,
                'order' => 7,
                'is_global' => true,
            ],
            [
                'name' => 'House Rules',
                'type' => CustomFieldEnum::TEXT,
                'order' => 8,
                'is_global' => true,
            ],
            [
                'name' => 'Amenities',
                'type' => CustomFieldEnum::DROPDOWN,
                'order' => 9,
                'is_global' => true,
                'options' => [
                    ['label' => 'WiFi', 'value' => 'wifi', 'order' => 1],
                    ['label' => 'Kitchen', 'value' => 'kitchen', 'order' => 2],
                    ['label' => 'Washing Machine', 'value' => 'washing_machine', 'order' => 3],
                    ['label' => 'Air Conditioning', 'value' => 'air_conditioning', 'order' => 4],
                    ['label' => 'Heating', 'value' => 'heating', 'order' => 5],
                    ['label' => 'TV', 'value' => 'tv', 'order' => 6],
                    ['label' => 'Parking', 'value' => 'parking', 'order' => 7],
                    ['label' => 'Pool', 'value' => 'pool', 'order' => 8],
                    ['label' => 'Hot Tub', 'value' => 'hot_tub', 'order' => 9],
                    ['label' => 'Gym', 'value' => 'gym', 'order' => 10],
                    ['label' => 'Pet Friendly', 'value' => 'pet_friendly', 'order' => 11],
                    ['label' => 'Smoking Allowed', 'value' => 'smoking_allowed', 'order' => 12],
                ],
            ],
            [
                'name' => 'Cancellation Policy',
                'type' => CustomFieldEnum::DROPDOWN,
                'order' => 10,
                'is_global' => true,
                'options' => [
                    ['label' => 'Flexible', 'value' => 'flexible', 'order' => 1],
                    ['label' => 'Moderate', 'value' => 'moderate', 'order' => 2],
                    ['label' => 'Strict', 'value' => 'strict', 'order' => 3],
                    ['label' => 'Super Strict', 'value' => 'super_strict', 'order' => 4],
                ],
            ],
        ];

        foreach ($vacationRentalFields as $fieldData) {
            $options = $fieldData['options'] ?? [];
            unset($fieldData['options']);

            $customField = CustomField::query()->create(array_merge($fieldData, [
                'authorable_type' => User::class,
                'authorable_id' => $adminUser->id,
            ]));

            if (!empty($options)) {
                foreach ($options as $optionData) {
                    CustomFieldOption::query()->create(array_merge($optionData, [
                        'custom_field_id' => $customField->id,
                    ]));
                }
            }
        }

        $this->command->info('Created vacation rental custom fields successfully.');
    }
}
