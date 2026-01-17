<?php

namespace App\Notifications;

use App\Models\Location;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LocationDeletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Location $location
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
        return FilamentNotification::make()
            ->title('Location Deleted')
            ->body("{$this->location->company->name} {$this->location->name} location has been permanently deleted.")
            ->danger()
            ->getDatabaseMessage();
    }
}
