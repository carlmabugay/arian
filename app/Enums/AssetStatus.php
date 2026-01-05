<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AssetStatus: string implements HasColor, HasLabel
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

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Available => 'success',
            self::Assigned => 'info',
            self::Maintenance => 'warning',
            self::Lost => 'danger',
            self::Disposed => 'gray',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }
}
