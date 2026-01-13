<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, UserRole::values());
    }

    public function view(User $user, Asset $asset): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isCompanyAdmin()) {
            return $user->company_id === $asset->company_id;
        }

        return $asset->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::CompanyAdmin,
        ]);
    }

    public function update(User $user, Asset $asset): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isCompanyAdmin() && $user->company_id === $asset->company_id;
    }

    public function delete(User $user, Asset $asset): bool
    {
        if (! in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::CompanyAdmin,
        ])) {
            return false;
        }

        if ($asset->isAssigned()) {
            return false;
        }

        if ($user->isCompanyAdmin()) {
            return $user->company_id === $asset->company_id;
        }

        return true;
    }

    public function forceDelete(User $user, Asset $asset): bool
    {
        return $user->isSuperAdmin() && ! $asset->assignments()->withTrashed()->exists();
    }

    public function restore(User $user, Asset $asset): bool
    {
        return $this->update($user, $asset);
    }

    public function deleteAny(User $user): bool
    {

        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::CompanyAdmin,
        ]);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
