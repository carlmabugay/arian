<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AssetCondition: string implements HasColor, HasLabel
{
    case New = 'new';
    case Good = 'good';
    case Fair = 'fair';
    case Damaged = 'damaged';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New => 'success',
            self::Good => 'info',
            self::Fair => 'warning',
            self::Damaged => 'danger',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }
}
