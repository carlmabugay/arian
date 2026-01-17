<?php

namespace App\Notifications;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $createdUser,
        public ?User $actor = null
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
            ->title('New user created')
            ->body($this->bodyFor($notifiable))
            ->actions($this->actionsFor($notifiable))
            ->success()
            ->getDatabaseMessage();
    }

    protected function bodyFor(object $recipient): string
    {
        if ($recipient->is($this->createdUser)) {
            return "Your account has been created under {$this->createdUser->company->name}.";
        }

        return "{$this->createdUser->name} was added to {$this->createdUser->company->name}.";
    }

    protected function actionsFor(object $recipient): array
    {
        if ($recipient->is($this->createdUser)) {
            return [];
        }

        // Admins can view users list
        return [
            Action::make('view')
                ->button()
                ->url(UserResource::getIndexUrl())
                ->markAsRead(),
        ];
    }
}
