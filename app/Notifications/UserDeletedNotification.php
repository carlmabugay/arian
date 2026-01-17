<?php

namespace App\Notifications;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserDeletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public ?User $actor = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('User permanently deleted')
            ->body($this->bodyFor())
            ->actions($this->actionsFor())
            ->danger()
            ->getDatabaseMessage();
    }

    protected function bodyFor(): string
    {
        return sprintf(
            '%s permanently deleted %s.',
            $this->actor?->name ?? 'System',
            $this->user->name
        );
    }

    protected function actionsFor(): array
    {
        return [
            Action::make('view')
                ->button()
                ->url(UserResource::getIndexUrl(['trashed' => true]))
                ->markAsRead(),
        ];
    }
}
