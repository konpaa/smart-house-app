<?php

namespace App\Enums;

enum DeviceStatus: string
{
    case ON = 'on';
    case OFF = 'off';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case STANDBY = 'standby';
    case ERROR = 'error';
    case UPDATING = 'updating';
    case MAINTENANCE = 'maintenance';

    /**
     * Get all values as array
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
