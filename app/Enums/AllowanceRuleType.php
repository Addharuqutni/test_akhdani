<?php

namespace App\Enums;

enum AllowanceRuleType: string
{
    case DISTANCE_0_60 = 'distance_0_60';
    case SAME_PROVINCE = 'same_province';
    case DIFFERENT_PROVINCE_SAME_ISLAND = 'different_province_same_island';
    case DIFFERENT_ISLAND = 'different_island';
    case FOREIGN = 'foreign';

    public function label(): string
    {
        return match ($this) {
            self::DISTANCE_0_60 => '0–60 km (Tanpa Uang Saku)',
            self::SAME_PROVINCE => '> 60 km, Provinsi Sama',
            self::DIFFERENT_PROVINCE_SAME_ISLAND => '> 60 km, Beda Provinsi (Satu Pulau)',
            self::DIFFERENT_ISLAND => '> 60 km, Beda Pulau',
            self::FOREIGN => 'Luar Negeri',
        };
    }

    public static function labelOf(?string $value): string
    {
        return self::tryFrom((string) $value)?->label() ?? '-';
    }
}
