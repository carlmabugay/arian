<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\UserCreatedNotification;
use App\Notifications\UserDeletedNotification;
use App\Notifications\UserRestoredNotification;
use App\Notifications\UserRoleChangedNotification;
use App\Notifications\UserTrashedNotification;
use App\Notifications\UserUpdatedNotification;
use App\Support\Notifications\OrganizationRecipients;
use Illuminate\Support\Arr;

class UserObserver
{
    protected const FIELDS_TO_WATCH = [
        'name',
        'email',
        'role',
        'company_id',
        'is_active',
    ];

    /**
     * Handle the User "created" event.
     */
    public function created(User $createdUser): void
    {
        $actor = auth()->user();
        $recipients = OrganizationRecipients::resolve($createdUser->company, $actor);

        foreach ($recipients as $recipient) {
            $recipient->notify(
                new UserCreatedNotification($createdUser, $actor)
            );
        }

        $createdUser->notify(
            new UserCreatedNotification($createdUser)
        );

    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('role')) {
            $this->handleRoleChanged($user);

            return;
        }

        $this->handleUserUpdated($user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if (! $user->trashed()) {
            return;
        }

        $actor = auth()->user();

        $recipients = OrganizationRecipients::resolve(
            $user->company,
            $actor
        );

        foreach ($recipients as $recipient) {
            $recipient->notify(
                new UserTrashedNotification(
                    user: $user,
                    actor: $actor
                )
            );
        }

        if (! $user->is($actor)) {
            $user->notify(
                new UserTrashedNotification(
                    user: $user,
                    actor: $actor,
                    forSelf: true
                )
            );
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $actor = auth()->user();

        $recipients = OrganizationRecipients::resolve(
            $user->company,
            $actor
        );

        foreach ($recipients as $recipient) {
            $recipient->notify(
                new UserRestoredNotification(
                    user: $user,
                    actor: $actor
                )
            );
        }

        // Notify the restored user
        if (! $user->is($actor)) {
            $user->notify(
                new UserRestoredNotification(
                    user: $user,
                    actor: $actor,
                    forSelf: true
                )
            );
        }
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $actor = auth()->user();

        $recipients = OrganizationRecipients::resolve(
            $user->company,
            $actor
        );

        foreach ($recipients as $recipient) {
            $recipient->notify(
                new UserDeletedNotification(
                    user: $user,
                    actor: $actor
                )
            );
        }
    }

    protected function handleRoleChanged(User $user): void
    {
        $actor = auth()->user();

        $oldRole = $user->getOriginal('role')->value;
        $newRole = $user->role->value;

        $recipients = OrganizationRecipients::resolve(
            $user->company,
            $actor
        );

        foreach ($recipients as $recipient) {
            $recipient->notify(
                new UserRoleChangedNotification(
                    user: $user,
                    oldRole: $oldRole,
                    newRole: $newRole,
                    actor: $actor
                )
            );
        }

        // Notify the affected user
        if (! $user->is($actor)) {
            $user->notify(
                new UserRoleChangedNotification(
                    user: $user,
                    oldRole: $oldRole,
                    newRole: $newRole,
                    actor: $actor,
                    forSelf: true
                )
            );
        }
    }

    protected function handleUserUpdated(User $user): void
    {
        $changes = Arr::only(
            $user->getChanges(),
            self::FIELDS_TO_WATCH
        );

        if (empty($changes)) {
            return;
        }

        $actor = auth()->user();

        $recipients = OrganizationRecipients::resolve(
            $user->company,
            $actor
        );

        foreach ($recipients as $recipient) {
            $recipient->notify(
                new UserUpdatedNotification(
                    user: $user,
                    changes: $changes,
                    actor: $actor
                )
            );
        }

        if (! $user->is($actor)) {
            $user->notify(
                new UserUpdatedNotification(
                    user: $user,
                    changes: $changes,
                    actor: $actor,
                    forSelf: true
                )
            );
        }

    }
}
