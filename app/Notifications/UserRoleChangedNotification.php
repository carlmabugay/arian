<?php

namespace App\Notifications;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserRoleChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $oldRole,
        public string $newRole,
        public ?User $actor = null,
        public bool $forSelf = false,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('User role changed')
            ->body($this->bodyFor())
            ->actions($this->actionsFor())
            ->warning()
            ->getDatabaseMessage();
    }

    protected function bodyFor(): string
    {
        if ($this->forSelf) {
            return sprintf(
                'Your role has been changed from %s to %s.',
                Str::headline($this->oldRole),
                Str::headline($this->newRole),
            );
        }

        return sprintf(
            '%s changed %s’s role from %s to %s.',
            $this->actor?->name ?? 'System',
            $this->user->name,
            Str::headline($this->oldRole),
            Str::headline($this->newRole),
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
