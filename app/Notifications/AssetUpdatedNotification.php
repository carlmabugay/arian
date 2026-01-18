<?php

namespace App\Notifications;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class AssetUpdatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Asset $asset,
        public ?User $actor,
        public array $changes,
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
        $actorName = $this->actor?->name ?? 'System';

        return FilamentNotification::make()
            ->title('Asset Updated')
            ->body(
                "{$actorName} updated asset: {$this->asset->name}.\n\n".
                $this->changesSummary()
            )
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(
                        AssetResource::getUrl('edit', [
                            'record' => $this->asset,
                        ])
                    )
                    ->markAsRead(),
            ])
            ->success()
            ->getDatabaseMessage();
    }

    protected function changesSummary(): string
    {
        return collect($this->changes)
            ->map(fn ($change, $field) => sprintf(
                '%s: %s → %s',
                Str::headline($field),
                $change['old']->value ?? '—',
                $change['new']->value ?? '—'
            ))
            ->implode("\n");
    }
}
