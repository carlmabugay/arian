<?php

namespace App\Filament\Traits;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

trait HasBulkActions
{
    protected static function guardBulkAction(Collection $records, string $ability, string $title, string $message): bool
    {
        $user = auth()->user();

        $blocked = $records->filter(
            fn ($record) => $user->cannot($ability, $record)
        );

        if ($blocked->isNotEmpty()) {
            Notification::make()
                ->title($title)
                ->body($message)
                ->danger()
                ->send();
        }

        return $blocked->isEmpty();
    }
}
