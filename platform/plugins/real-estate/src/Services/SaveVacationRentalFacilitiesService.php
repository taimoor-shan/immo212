<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\VacationRental;

class SaveVacationRentalFacilitiesService
{
    public function execute(VacationRental $vacationRental, array|string|null $facilities): void
    {
        $vacationRental->facilities()->detach();

        if (! $facilities || ! is_array($facilities)) {
            return;
        }

        $facilitiesToSync = [];

        foreach ($facilities as $facility) {
            if (empty($facility['id']) || $facility['id'] == '0') {
                continue;
            }

            $facilitiesToSync[$facility['id']] = [
                'distance' => $facility['distance'],
            ];
        }

        if (empty($facilitiesToSync)) {
            return;
        }

        $vacationRental->facilities()->syncWithoutDetaching($facilitiesToSync);
    }
}
