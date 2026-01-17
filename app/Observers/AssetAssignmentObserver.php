<?php

namespace App\Observers;

use App\Models\AssetAssignment;
use App\Notifications\AssetAssignedNotification;
use App\Notifications\AssetReassignedNotification;
use App\Notifications\AssetReturnedNotification;

class AssetAssignmentObserver
{
    /**
     * Handle the AssetAssignment "created" event.
     */
    public function created(AssetAssignment $assetAssignment): void
    {
        $assetAssignment->user->notify(new AssetAssignedNotification($assetAssignment));
    }

    /**
     * Handle the AssetAssignment "updated" event.
     */
    public function updated(AssetAssignment $assetAssignment): void
    {

        if ($assetAssignment->wasChanged('user_id')) {
            $this->handleUserChange($assetAssignment);
        }

        if ($assetAssignment->wasChanged('asset_id')) {
            $this->created($assetAssignment);
        }

        if (
            $assetAssignment->wasChanged('returned_at') &&
            $assetAssignment->returned_at !== null
        ) {
            $this->handleReturn($assetAssignment);
        }

    }

    /**
     * Handle the AssetAssignment "deleted" event.
     */
    public function deleted(AssetAssignment $assetAssignment): void
    {
        //
    }

    /**
     * Handle the AssetAssignment "restored" event.
     */
    public function restored(AssetAssignment $assetAssignment): void
    {
        //
    }

    /**
     * Handle the AssetAssignment "force deleted" event.
     */
    public function forceDeleted(AssetAssignment $assetAssignment): void
    {
        //
    }

    private function handleUserChange(AssetAssignment $assignment): void
    {
        $oldUserId = $assignment->getOriginal('user_id');
        $newUser = $assignment->user;

        if ($oldUserId) {
            $oldUser = $assignment->user()->getModel()::find($oldUserId);

            if ($oldUser) {
                $oldUser->notify(new AssetReassignedNotification($assignment));
            }
        }

        $newUser->notify(new AssetAssignedNotification($assignment));
    }

    private function handleReturn(AssetAssignment $assignment): void
    {
        $user = $assignment->user;

        if (! $user) {
            return;
        }
        $user->notify(new AssetReturnedNotification($assignment));
    }
}
