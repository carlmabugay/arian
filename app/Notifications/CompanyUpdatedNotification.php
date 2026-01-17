<?php

namespace App\Notifications;

use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Company;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompanyUpdatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Company $company
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
            ->title('Company Updated')
            ->body("Company {$this->company->name} has been updated.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(CompanyResource::getIndexUrl())
                    ->markAsRead(),
            ])
            ->success()
            ->getDatabaseMessage();
    }
}
