<?php

namespace App\Filament\Traits;

use App\Enums\SystemAction;
use App\Helpers\SystemMessageHelper;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

trait ConfigureSystemAction
{
    public static function configureAction(Action $action, SystemAction $systemAction, string $resourceName): Action
    {
        return $action
            ->requiresConfirmation(
                in_array($systemAction, [
                    SystemAction::Delete,
                    SystemAction::Restore,
                    SystemAction::ForceDelete,
                ])
            )
            ->modalHeading(
                SystemMessageHelper::confirmHeading($systemAction, $resourceName)
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody($systemAction, $resourceName)
            )
            ->successNotification(
                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle($systemAction, $resourceName)
                    )
                    ->success()
            );
    }
}
