<?php

namespace App\Observers;

use App\Models\Asset;
use App\Notifications\AssetCreatedNotification;
use App\Notifications\AssetDeletedNotification;
use App\Notifications\AssetRestoredNotification;
use App\Notifications\AssetTrashedNotification;
use App\Notifications\AssetUpdatedNotification;
use App\Support\Notifications\AssetRecipients;
use Illuminate\Support\Arr;

class AssetObserver
{
    protected const FIELDS_TO_WATCH = [
        'asset_category_id',
        'status',
        'condition',
        'asset_tag',
        'serial_number',
        'name',
        'description',
        'purchased_at',
        'purchase_price',
        'location_id',
    ];

    /**
     * Handle the Asset "created" event.
     */
    public function created(Asset $asset): void
    {
        $actor = auth()->user();

        $recipients = AssetRecipients::resolve(
            asset: $asset,
            actor: $actor,
        );

        foreach ($recipients as $user) {
            $user->notify(
                new AssetCreatedNotification(
                    asset: $asset,
                    actor: $actor,
                )
            );
        }
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        $changes = Arr::only(
            $asset->getChanges(),
            self::FIELDS_TO_WATCH
        );

        if (empty($changes)) {
            return;
        }

        $actor = auth()->user();

        $recipients = AssetRecipients::resolve(
            asset: $asset,
            actor: $actor,
        );

        foreach ($recipients as $user) {
            $user->notify(
                new AssetUpdatedNotification(
                    asset: $asset,
                    actor: $actor,
                    changes: $this->formatChanges($asset),
                )
            );
        }
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        if (! $asset->trashed()) {
            return;
        }

        $actor = auth()->user();

        $recipients = AssetRecipients::resolve(
            asset: $asset,
            actor: $actor,
        );

        foreach ($recipients as $user) {
            $user->notify(
                new AssetTrashedNotification(
                    asset: $asset,
                    actor: $actor,
                )
            );
        }
    }

    /**
     * Handle the Asset "restored" event.
     */
    public function restored(Asset $asset): void
    {

        $actor = auth()->user();

        $recipients = AssetRecipients::resolve(
            asset: $asset,
            actor: $actor,
        );

        foreach ($recipients as $user) {
            $user->notify(
                new AssetRestoredNotification(
                    asset: $asset,
                    actor: $actor,
                )
            );
        }
    }

    /**
     * Handle the Asset "force deleted" event.
     */
    public function forceDeleted(Asset $asset): void
    {
        $actor = auth()->user();

        $recipients = AssetRecipients::resolve(
            asset: $asset,
            actor: $actor,
        );

        foreach ($recipients as $user) {
            $user->notify(
                new AssetDeletedNotification(
                    asset: $asset,
                    actor: $actor,
                )
            );
        }
    }

    protected function formatChanges(Asset $asset): array
    {
        return collect($asset->getChanges())
            ->map(fn ($new, $field) => [
                'old' => $asset->getOriginal($field),
                'new' => $new,
            ])
            ->only(self::FIELDS_TO_WATCH)
            ->toArray();
    }
}
