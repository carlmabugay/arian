<?php

namespace App\Helpers;

use App\Enums\SystemAction;
use Illuminate\Support\Str;

class SystemMessageHelper
{
    public static function confirmHeading(SystemAction $action, string $resource): string
    {
        return match ($action) {
            SystemAction::Delete => "Move {$resource} to trash?",
            SystemAction::Restore => "Restore {$resource}?",
            SystemAction::ForceDelete => "Permanently delete {$resource}?",
            default => "{$action->getLabel()} {$resource}",
        };
    }

    public static function confirmBody(SystemAction $action, string $resource): ?string
    {
        return match ($action) {
            SystemAction::Delete => 'You can restore this later.',
            SystemAction::ForceDelete => 'This action is irreversible.',
            default => null,
        };
    }

    public static function successTitle(SystemAction $action, string $resource): string
    {
        return "{$resource} {$action->pastTense()}";
    }

    public static function successBody(SystemAction $action, int $count, string $noun = 'record'): ?string
    {
        if ($count <= 1) {
            return null;
        }

        return sprintf('%d %s were %s', $count, Str::plural($noun, $count), $action->pastTense());
    }

    public static function bulkBlockedTitle(SystemAction $action): string
    {
        return match ($action) {
            SystemAction::Delete => 'Trashed blocked',
            SystemAction::Restore => 'Restore blocked',
            SystemAction::ForceDelete => 'Permanent delete blocked',
            default => 'Action blocked',
        };
    }

    public static function bulkBlockedBody(SystemAction $action, string $noun): string
    {
        return match ($action) {
            SystemAction::Delete => "One or more selected {$noun} cannot be trashed.",
            SystemAction::Restore => "One or more selected {$noun} cannot be restored.",
            SystemAction::ForceDelete => "One or more selected {$noun} cannot be permanently deleted.",
            default => "One or more selected {$noun} cannot be processed.",
        };
    }
}
