<?php

namespace App\Patterns\Creational\AbstractFactory\Enums;

enum DeviceBrand: string
{
    case XIAOMI = 'xiaomi';
    case PHILIPS_HUE = 'philips';

    /**
     * Get display name for the brand
     */
    public function displayName(): string
    {
        return match ($this) {
            self::XIAOMI => 'Xiaomi',
            self::PHILIPS_HUE => 'Philips Hue',
        };
    }

    /**
     * Get all values as array
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all display names as array
     *
     * @return array<string>
     */
    public static function displayNames(): array
    {
        return array_map(fn ($case) => $case->displayName(), self::cases());
    }
}
