<?php

namespace App\Enums;

enum DeviceType: string
{
    case LIGHT = 'light';
    case SENSOR = 'sensor';
    case THERMOSTAT = 'thermostat';
    case CAMERA = 'camera';
    case DOOR_LOCK = 'door_lock';
    case WINDOW = 'window';
    case BLINDS = 'blinds';
    case SWITCH = 'switch';
    case OUTLET = 'outlet';
    case SPEAKER = 'speaker';
    case TV = 'tv';
    case AIR_CONDITIONER = 'air_conditioner';
    case HEATER = 'heater';
    case FAN = 'fan';
    case VACUUM = 'vacuum';
    case SMOKE_DETECTOR = 'smoke_detector';
    case MOTION_SENSOR = 'motion_sensor';
    case DOORBELL = 'doorbell';
    case GARAGE_DOOR = 'garage_door';
    case IRRIGATION = 'irrigation';
    case SECURITY_ALARM = 'security_alarm';

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
