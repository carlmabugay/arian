<?php

namespace App\Support\Notifications;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Collection;

class OrganizationRecipients
{
    public static function resolve(Company $company, ?User $actor = null): Collection
    {
        $recipients = User::superAdmin()
            ->get()
            ->merge(
                $company->users()->companyAdmin()->get()
            );

        if ($actor) {
            $recipients = $recipients->reject(
                fn (User $user) => $user->id === $actor->id
            );
        }

        return $recipients->unique('id')->values();
    }
}
