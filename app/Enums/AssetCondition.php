<?php

namespace App\Enums;

enum AssetCondition: string
{
    case New = 'new';
    case Good = 'good';
    case Fair = 'fair';
    case Damaged = 'damaged';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
