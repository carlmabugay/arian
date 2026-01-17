<?php

namespace App\Support\Notifications;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Collection;

class OrganizationRecipients
{
    public static function resolve(Company $company): Collection
    {
        return User::superAdmin()
            ->get()
            ->merge(
                $company->users()->companyAdmin()->get()
            )
            ->unique('id')
            ->values();
    }
}
