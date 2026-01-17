<?php

namespace App\Notifications;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\AssetAssignment;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssetAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public AssetAssignment $assetAssignment,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->title('New asset assignment!')
            ->body("Asset {$this->assetAssignment->asset->name} has been assigned to you.")
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
