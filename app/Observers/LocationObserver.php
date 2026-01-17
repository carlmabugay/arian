<?php

namespace App\Observers;

use App\Models\Location;
use App\Models\User;
use App\Notifications\LocationCreatedNotification;
use App\Notifications\LocationDeletedNotification;
use App\Notifications\LocationRestoredNotification;
use App\Notifications\LocationTrashedNotification;
use App\Notifications\LocationUpdatedNotification;
use App\Support\Notifications\OrganizationRecipients;

class LocationObserver
{
    /**
     * Handle the Location "created" event.
     */
    public function created(Location $location): void
    {
        $recipients = OrganizationRecipients::resolve($location->company);

        foreach ($recipients as $user) {
            $user->notify(new LocationCreatedNotification($location));
        }
    }

    /**
     * Handle the Location "updated" event.
     */
    public function updated(Location $location): void
    {
        if ($location->wasChanged('deleted_at')) {
            return;
        }

        $recipients = OrganizationRecipients::resolve($location->company);

        foreach ($recipients as $user) {
            $user->notify(new LocationUpdatedNotification($location));
        }
    }

    /**
     * Handle the Location "deleted" event.
     */
    public function deleted(Location $location): void
    {
        if (! $location->trashed()) {
            return;
        }

        $recipients = OrganizationRecipients::resolve($location->company);

        foreach ($recipients as $user) {
            $user->notify(new LocationTrashedNotification($location));
        }
    }

    /**
     * Handle the Location "restored" event.
     */
    public function restored(Location $location): void
    {
        $recipients = OrganizationRecipients::resolve($location->company);

        foreach ($recipients as $user) {
            $user->notify(new LocationRestoredNotification($location));
        }
    }

    /**
     * Handle the Location "force deleted" event.
     */
    public function forceDeleted(Location $location): void
    {
        $superAdmins = User::superAdmin()->get();

        foreach ($superAdmins as $user) {
            $user->notify(new LocationDeletedNotification($location));
        }
    }
}
