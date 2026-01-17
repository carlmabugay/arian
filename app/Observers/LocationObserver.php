<?php

namespace App\Observers;

use App\Models\Location;
use App\Models\User;
use App\Notifications\LocationCreatedNotification;
use App\Notifications\LocationDeletedNotification;
use App\Notifications\LocationRestoredNotification;
use App\Notifications\LocationTrashedNotification;
use App\Notifications\LocationUpdatedNotification;
use Illuminate\Support\Collection;

class LocationObserver
{
    /**
     * Handle the Location "created" event.
     */
    public function created(Location $location): void
    {
        $recipients = $this->getRecipients($location);

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

        $recipients = $this->getRecipients($location);

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

        $recipients = $this->getRecipients($location);

        foreach ($recipients as $user) {
            $user->notify(new LocationTrashedNotification($location));
        }
    }

    /**
     * Handle the Location "restored" event.
     */
    public function restored(Location $location): void
    {
        $recipients = $this->getRecipients($location);

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

    protected function getRecipients(Location $location): Collection
    {
        $recipients = collect(User::superAdmin()->get());

        $companyAdmin = $location->company->users()->companyAdmin()->first();
        if ($companyAdmin) {
            $recipients->push($companyAdmin);
        }

        return $recipients;
    }
}
