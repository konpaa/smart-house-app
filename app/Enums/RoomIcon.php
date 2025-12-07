<?php

namespace App\Enums;

enum RoomIcon: string
{
    case BEDROOM = 'bedroom';
    case KITCHEN = 'kitchen';
    case LIVING_ROOM = 'living-room';
    case BATHROOM = 'bathroom';
    case DINING_ROOM = 'dining-room';
    case OFFICE = 'office';
    case GARAGE = 'garage';
    case BASEMENT = 'basement';
    case ATTIC = 'attic';
    case BALCONY = 'balcony';
    case HALLWAY = 'hallway';
    case LAUNDRY = 'laundry';
    case STORAGE = 'storage';
    case GARDEN = 'garden';
    case POOL = 'pool';

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
