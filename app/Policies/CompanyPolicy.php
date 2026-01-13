<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::CompanyAdmin,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Company $company): bool
    {
        return $this->canManageCompany($user, $company);
    }

    public function delete(User $user, Company $company): bool
    {
        if (! $this->canManageCompany($user, $company)) {
            return false;
        }

        return ! $company->hasAnyChildren();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Company $company): bool
    {
        return $user->isSuperAdmin() && $company->trashed();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isSuperAdmin();

    }

    public function forceDelete(User $user, Company $company): bool
    {
        if (! $user->isSuperAdmin()) {
            return false;
        }

        return ! $company->hasAnyChildren();
    }

    protected function canManageCompany(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isCompanyAdmin() && $user->company_id === $company->id;
    }
}
