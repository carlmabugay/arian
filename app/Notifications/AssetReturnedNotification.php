<?php

namespace App\Notifications;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\AssetAssignment;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssetReturnedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    /**
     * Create a new notification instance.
     */
    public function __construct(
        public AssetAssignment $assetAssignment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Asset returned')
            ->body("{$this->assetAssignment->asset->name} has been returned.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(AssetResource::getIndexUrl())
                    ->markAsRead(),
            ])
            ->success()
            ->getDatabaseMessage();

    }
}
