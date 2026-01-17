<?php

namespace App\Notifications;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserUpdatedNotification extends Notification
{
    public function __construct(
        public User $user,
        public array $changes,
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
            ->title('User updated')
            ->body($this->bodyFor())
            ->actions($this->actionsFor())
            ->success()
            ->getDatabaseMessage();
    }

    protected function bodyFor(): string
    {

        if ($this->forSelf) {
            return 'Your account details have been updated.';
        }

        return sprintf(
            '%s updated %s (%s).',
            $this->actor?->name ?? 'System',
            $this->user->name,
            $this->formattedChanges()
        );
    }

    protected function formattedChanges(): string
    {
        return collect($this->changes)
            ->keys()
            ->map(fn ($field) => Str::headline($field))
            ->implode(', ');
    }

    protected function actionsFor(): array
    {
        if ($this->forSelf) {
            return [];
        }

        return [
            Action::make('view')
                ->button()
                ->url(UserResource::getIndexUrl())
                ->markAsRead(),
        ];
    }
}
