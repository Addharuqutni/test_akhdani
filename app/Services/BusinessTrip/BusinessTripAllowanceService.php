<?php

namespace App\Services\BusinessTrip;

use App\Enums\AllowanceRuleType;
use App\Models\City;

class BusinessTripAllowanceService
{
    public function calculate(City $origin, City $destination, float $distanceKm, int $durationDays): array
    {
        $rule = AllowanceRuleType::DISTANCE_0_60;
        $currency = 'IDR';
        $perDay = 0;

        if ($destination->is_foreign) {
            $rule = AllowanceRuleType::FOREIGN;
            $currency = 'USD';
            $perDay = 50;
        } elseif ($distanceKm > 60) {
            if ($origin->province_name === $destination->province_name) {
                $rule = AllowanceRuleType::SAME_PROVINCE;
                $perDay = 200000;
            } elseif ($origin->island_name === $destination->island_name) {
                $rule = AllowanceRuleType::DIFFERENT_PROVINCE_SAME_ISLAND;
                $perDay = 250000;
            } else {
                $rule = AllowanceRuleType::DIFFERENT_ISLAND;
                $perDay = 300000;
            }
        }

        return [
            'allowance_rule_type' => $rule->value,
            'allowance_currency' => $currency,
            'allowance_per_day' => $perDay,
            'allowance_total' => $perDay * $durationDays,
        ];
    }
}
