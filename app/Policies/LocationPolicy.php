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
        return $user->role === UserRole::SuperAdmin;
    }

    public function restore(User $user, Location $location): bool
    {
        return
            $user->role === UserRole::SuperAdmin &&
            $location->trashed();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function forceDelete(User $user, Location $location): bool
    {
        if ($user->role !== UserRole::SuperAdmin) {
            return false;
        }

        return ! $location->assets()->withTrashed()->exists();
    }

    protected function canManageLocation(User $user, Location $location): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        return
            $user->role === UserRole::CompanyAdmin &&
            $user->company_id === $location->company_id;
    }
}
