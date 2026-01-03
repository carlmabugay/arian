<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum LocationType: string implements HasColor, HasLabel
{
    case Office = 'office';
    case Remote = 'remote';
    case Warehouse = 'warehouse';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Office => 'success',
            self::Remote => 'info',
            self::Warehouse => 'gray',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }
}
