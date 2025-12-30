<?php

namespace App\Enums;

enum LocationType: string
{
    case Office = 'office';
    case Remote = 'remote';
    case Warehouse = 'work';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
