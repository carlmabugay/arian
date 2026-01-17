<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\User;
use App\Notifications\CompanyCreatedNotification;
use App\Notifications\CompanyDeletedNotification;
use App\Notifications\CompanyRestoredNotification;
use App\Notifications\CompanyTrashedNotification;
use App\Notifications\CompanyUpdatedNotification;
use App\Support\Notifications\OrganizationRecipients;

class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        $superAdmins = User::superAdmin()->get();

        foreach ($superAdmins as $user) {
            $user->notify(new CompanyCreatedNotification($company));
        }
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        if ($company->wasChanged('deleted_at')) {
            return;
        }

        $recipients = OrganizationRecipients::resolve($company);

        foreach ($recipients as $user) {
            $user->notify(new CompanyUpdatedNotification($company));
        }
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(Company $company): void
    {
        if (! $company->trashed()) {
            return;
        }

        $recipients = OrganizationRecipients::resolve($company);

        foreach ($recipients as $user) {
            $user->notify(new CompanyTrashedNotification($company));
        }
    }

    /**
     * Handle the Company "restored" event.
     */
    public function restored(Company $company): void
    {
        $recipients = OrganizationRecipients::resolve($company);

        foreach ($recipients as $user) {
            $user->notify(new CompanyRestoredNotification($company));
        }
    }

    /**
     * Handle the Company "force deleted" event.
     */
    public function forceDeleted(Company $company): void
    {
        $superAdmins = User::superAdmin()->get();

        foreach ($superAdmins as $user) {
            $user->notify(new CompanyDeletedNotification($company));
        }
    }
}
