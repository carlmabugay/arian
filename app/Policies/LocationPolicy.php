<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Location;
use App\Models\User;

class LocationPolicy
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
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::CompanyAdmin,
        ]);
    }

    public function update(User $user, Location $location): bool
    {
        return $this->canManageLocation($user, $location);
    }

    public function delete(User $user, Location $location): bool
    {
        if (! $this->canManageLocation($user, $location)) {
            return false;
        }

        return ! $location->assets()->active()->exists();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Location $location): bool
    {
        return $user->isSuperAdmin() && $location->trashed();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Location $location): bool
    {
        if (! $user->isSuperAdmin()) {
            return false;
        }

        return ! $location->assets()->withTrashed()->exists();
    }

    protected function canManageLocation(User $user, Location $location): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isCompanyAdmin() && $user->company_id === $location->company_id;
    }
}
