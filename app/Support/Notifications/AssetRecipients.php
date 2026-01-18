<?php

namespace App\Support\Notifications;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Collection;

class AssetRecipients
{
    public static function resolve(
        Asset $asset,
        ?User $actor = null,
        ?User $assignedUser = null,
        ?User $oldAssignedUser = null
    ): Collection {
        $recipients = collect();

        $recipients = $recipients->merge(User::superAdmin()->get());

        $recipients = $recipients->merge(
            $asset->company->users()->companyAdmin()->get()
        );

        if ($assignedUser) {
            $recipients->push($assignedUser);
        }

        if ($oldAssignedUser) {
            $recipients->push($oldAssignedUser);
        }

        return $recipients
            ->unique('id')
            ->reject(fn ($user) => $actor && $user->id === $actor->id)
            ->values();
    }
}
