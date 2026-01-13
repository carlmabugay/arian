<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum SystemAction: string implements HasLabel
{
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
    case Restore = 'restore';
    case ForceDelete = 'force_delete';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Create => 'Create',
            self::Update => 'Save changes',
            self::Delete => 'Move to trash',
            self::Restore => 'Restore',
            self::ForceDelete => 'Permanently delete',
        };
    }

    public function pastTense(): string
    {
        return match ($this) {
            self::Create => 'created',
            self::Update => 'updated',
            self::Delete => 'moved to trash',
            self::Restore => 'restored',
            self::ForceDelete => 'permanently deleted',
        };
    }
}
