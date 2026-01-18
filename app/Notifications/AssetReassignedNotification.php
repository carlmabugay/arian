<?php

namespace App\Notifications;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssetReassignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Asset $asset,
        public ?User $oldUser,
        public User $newUser,
        public ?User $actor,
        public string $recipientType
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(User $notifiable): array
    {
        $actorName = $this->actor?->name ?? 'System';
        $oldName = $this->oldUser?->name ?? '—';
        $newName = $this->newUser->name;

        [$title, $body] = match ($this->recipientType) {
            'old' => [
                'Asset Reassigned',
                "{$this->asset->name} has been reassigned to {$newName}.",
            ],
            'new' => [
                'Asset Assigned to You',
                "{$this->asset->name} has been assigned to you by {$actorName}.",
            ],
            default => [
                'Asset Reassigned',
                "{$actorName} reassigned {$this->asset->name} from {$oldName} to {$newName}.",
            ],
        };

        return FilamentNotification::make()
            ->title($title)
            ->body($body)
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
