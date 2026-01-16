<?php

namespace App\Observers;

use App\Models\AssetAssignment;
use Filament\Notifications\Notification;

class AssetAssignmentObserver
{
    /**
     * Handle the AssetAssignment "created" event.
     */
    public function created(AssetAssignment $assetAssignment): void
    {
        $user = $assetAssignment->user;
        $assetName = $assetAssignment->asset->name;

        Notification::make()
            ->title('Asset assigned')
            ->body("Asset {$assetName} has been assigned to you.")
            ->sendToDatabase($user);
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
