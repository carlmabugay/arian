<?php

namespace App\Notifications;

use App\Filament\Resources\Locations\LocationResource;
use App\Models\Location;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LocationCreatedNotification extends Notification
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
            ->title('New Location Created')
            ->body("{$this->location->name} location has been created for {$this->location->company->name}.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(LocationResource::getIndexUrl())
                    ->markAsRead(),
            ])
            ->success()
            ->getDatabaseMessage();
    }
}
