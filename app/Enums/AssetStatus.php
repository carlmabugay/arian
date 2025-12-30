<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Available = 'available';
    case Assigned = 'assigned';
    case Maintenance = 'maintenance';
    case Lost = 'lost';
    case Disposed = 'disposed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
