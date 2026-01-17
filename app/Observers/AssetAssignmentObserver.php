<?php

namespace App\Observers;

use App\Models\AssetAssignment;
use App\Notifications\AssetAssignedNotification;

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
        //
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
}
