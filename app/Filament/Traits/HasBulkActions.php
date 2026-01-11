<?php

namespace App\Filament\Traits;

use App\Enums\SystemAction;
use App\Helpers\SystemMessageHelper;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

trait HasBulkActions
{
    protected static function guardBulkAction(Collection $records, string $ability, SystemAction $systemAction, string $noun): bool
    {
        $user = auth()->user();

        $blocked = $records->filter(
            fn ($record) => $user->cannot($ability, $record)
        );

        if ($blocked->isNotEmpty()) {
            Notification::make()
                ->title(
                    SystemMessageHelper::bulkBlockedTitle($systemAction)
                )
                ->body(
                    SystemMessageHelper::bulkBlockedBody($systemAction, $noun)
                )
                ->danger()
                ->send();
        }

        return $blocked->isEmpty();
    }
}
