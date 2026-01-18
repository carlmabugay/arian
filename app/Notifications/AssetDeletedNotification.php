<?php

namespace App\Notifications;

use App\Models\Asset;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssetDeletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Asset $asset,
        public ?User $actor,
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

    public function toDatabase(object $notifiable): array
    {
        $actorName = $this->actor?->name ?? 'System';

        return FilamentNotification::make()
            ->title('Asset Permanently Deleted')
            ->body("{$actorName} permanently deleted asset: {$this->asset->name}.")
            ->danger()
            ->getDatabaseMessage();
    }
}
