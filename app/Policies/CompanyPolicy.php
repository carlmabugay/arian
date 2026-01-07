<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function update(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function delete(User $user, Company $company): bool
    {
        if (! in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::CompanyAdmin,
        ])) {
            return false;
        }

        if (
            $user->role === UserRole::CompanyAdmin &&
            $user->company_id !== $company->id
        ) {
            return false;
        }

        return ! $company->assets()->active()->exists();
    }

    public function restoreAny(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function restore(User $user, Company $company): bool
    {
        if ($user->role !== UserRole::SuperAdmin) {
            return false;
        }

        return $company->trashed();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;

    }

    public function forceDelete(User $user, Company $company): bool
    {
        if (! in_array($user->role, [
            UserRole::SuperAdmin,
        ])) {
            return false;
        }

        return ! $company->hasAnyChildren();
    }
}
