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

    public function view(User $user, Company $company): bool
    {
        return $this->canManageCompany($user, $company);
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
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
        return $user->role === UserRole::SuperAdmin;
    }

    public function restore(User $user, Company $company): bool
    {
        return $user->role === UserRole::SuperAdmin && $company->trashed();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;

    }

    public function forceDelete(User $user, Company $company): bool
    {
        if ($user->role !== UserRole::SuperAdmin) {
            return false;
        }

        return ! $company->hasAnyChildren();
    }

    protected function canManageCompany(User $user, Company $company): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        return
            $user->role === UserRole::CompanyAdmin &&
            $user->company_id === $company->id;
    }
}
