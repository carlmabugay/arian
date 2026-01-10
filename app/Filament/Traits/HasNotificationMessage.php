<?php

namespace App\Filament\Traits;

use Illuminate\Support\Str;

trait HasNotificationMessage
{
    protected static function notificationMessage(int $count, string $action, string $noun = ''): string
    {
        $verb = $count === 1 ? 'was' : 'were';

        return sprintf(
            '%d %s %s %s.',
            $count,
            Str::plural($noun, $count),
            $verb,
            $action,
        );
    }
}
