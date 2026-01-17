<?php

namespace App\Notifications;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserRestoredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public ?User $actor = null,
        public bool $forSelf = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('User restored')
            ->body($this->bodyFor())
            ->actions($this->actionsFor())
            ->success()
            ->getDatabaseMessage();
    }

    protected function bodyFor(): string
    {
        if ($this->forSelf) {
            return 'Your account has been restored.';
        }

        return sprintf(
            '%s restored %s.',
            $this->actor?->name ?? 'System',
            $this->user->name
        );
    }

    protected function actionsFor(): array
    {
        return [
            Action::make('view')
                ->button()
                ->url(UserResource::getIndexUrl())
                ->markAsRead(),
        ];
    }
}
