<?php

namespace App\Notifications;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
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
        public Asset $asset,
        public AssetAssignment $assignment,
        public ?User $actor,
        public string $recipientType = 'assignee'
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
        $actorName = $this->actor?->name ?? 'System';

        $title = $this->recipientType === 'assignee'
            ? 'Asset Assigned to You'
            : 'Asset Assigned';

        $body = $this->recipientType === 'assignee'
            ? "{$this->asset->name} has been assigned to you by {$actorName}."
            : "{$actorName} assigned {$this->asset->name} to {$this->assignment->user->name}.";

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
