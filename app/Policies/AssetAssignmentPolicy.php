<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AssetAssignment;
use App\Models\User;

class AssetAssignmentPolicy
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

    public function update(User $user, AssetAssignment $assignment): bool
    {
        return match ($user->role) {
            UserRole::SuperAdmin => true,
            UserRole::CompanyAdmin => $user->company_id === $assignment->asset->company_id,
            default => false,
        };
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, AssetAssignment $assignment): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, AssetAssignment $assignment): bool
    {
        return false;
    }
}
