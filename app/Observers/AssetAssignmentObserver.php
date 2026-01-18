<?php

namespace App\Observers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Notifications\AssetAssignedNotification;
use App\Notifications\AssetReassignedNotification;
use App\Notifications\AssetReturnedNotification;
use App\Support\Notifications\AssetRecipients;

class AssetAssignmentObserver
{
    protected ?int $previousUserId = null;

    /**
     * Handle the AssetAssignment "created" event.
     */
    public function created(AssetAssignment $assignment): void
    {
        $actor = $assignment->assignedBy;
        $asset = $assignment->asset;
        $newUser = $assignment->user;

        $oldUser = $assignment->getOriginal('user_id') ? User::find($assignment->getOriginal('user_id')) : null;

        if ($oldUser) {
            $oldUser->notify(
                new AssetReassignedNotification(
                    asset: $asset,
                    oldUser: $oldUser,
                    newUser: $newUser,
                    actor: $actor,
                    recipientType: 'old',
                )
            );

            $newUser->notify(
                new AssetReassignedNotification(
                    asset: $asset,
                    oldUser: $oldUser,
                    newUser: $newUser,
                    actor: $actor,
                    recipientType: 'new',
                )
            );

            $recipients = AssetRecipients::resolve(
                asset: $asset,
                actor: $actor,
                assignedUser: $newUser,
                oldAssignedUser: $oldUser,
            );

            foreach ($recipients as $user) {
                $user->notify(
                    new AssetReassignedNotification(
                        asset: $asset,
                        oldUser: $oldUser,
                        newUser: $newUser,
                        actor: $actor,
                        recipientType: 'admin',
                    )
                );
            }
        } else {
            $newUser->notify(
                new AssetAssignedNotification(
                    asset: $asset,
                    assignment: $assignment,
                    actor: $actor,
                    recipientType: 'assignee',
                )
            );

            $recipients = AssetRecipients::resolve(
                asset: $asset,
                actor: $actor,
                assignedUser: $newUser
            );

            foreach ($recipients as $user) {
                $user->notify(
                    new AssetAssignedNotification(
                        asset: $asset,
                        assignment: $assignment,
                        actor: $actor,
                        recipientType: 'admin',
                    )
                );
            }
        }
    }

    /**
     * Handle the AssetAssignment "updated" event.
     */
    public function updated(AssetAssignment $assignment): void
    {
        $actor = auth()->user();
        $asset = $assignment->asset;

        if ($assignment->wasChanged('user_id')) {
            $oldUserId = $assignment->getOriginal('user_id');
            $oldUser = $oldUserId ? $assignment->user()->find($oldUserId) : null;
            $newUser = $assignment->user;

            $this->notifyReassignment($asset, $oldUser, $newUser, $actor);

            $recipients = AssetRecipients::resolve(
                asset: $asset,
                actor: $actor,
                assignedUser: $newUser,
                oldAssignedUser: $oldUser
            );

            foreach ($recipients as $user) {
                $user->notify(
                    new AssetReassignedNotification(
                        asset: $asset,
                        oldUser: $oldUser,
                        newUser: $newUser,
                        actor: $actor,
                        recipientType: 'admin',
                    )
                );
            }
        }

        if ($assignment->wasChanged('asset_id')) {
            $this->created($assignment);
        }

        if ($assignment->wasChanged('returned_at') && $assignment->returned_at !== null) {
            $assignment->user?->notify(new AssetReturnedNotification($assignment));
        }
    }

    /**
     * Notify old and new users about reassignment.
     */
    private function notifyReassignment(Asset $asset, ?User $oldUser, User $newUser, ?User $actor): void
    {
        $oldUser?->notify(
            new AssetReassignedNotification(
                asset: $asset,
                oldUser: $oldUser,
                newUser: $newUser,
                actor: $actor,
                recipientType: 'old',
            )
        );

        $newUser->notify(
            new AssetReassignedNotification(
                asset: $asset,
                oldUser: $oldUser,
                newUser: $newUser,
                actor: $actor,
                recipientType: 'new',
            )
        );
    }

    /**
     * Handle the AssetAssignment "deleted" event.
     */
    public function deleted(AssetAssignment $assignment): void
    {
        //
    }

    /**
     * Handle the AssetAssignment "restored" event.
     */
    public function restored(AssetAssignment $assignment): void
    {
        //
    }

    /**
     * Handle the AssetAssignment "force deleted" event.
     */
    public function forceDeleted(AssetAssignment $assignment): void
    {
        //
    }
}
