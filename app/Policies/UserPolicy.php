<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::CompanyAdmin]);
    }

    public function view(User $user, User $model): bool
    {
        return $this->canManageUser($user, $model);
    }

    public function update(User $user, User $model): bool
    {
        return $this->canManageUser($user, $model);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::CompanyAdmin]);
    }

    public function delete(User $user, User $model): bool
    {
        if (! $user->isSuperAdmin()) {
            return false;
        }

        return ! $model->hasActiveAssignments();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->isSuperAdmin();
    }

    protected function canManageUser(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isCompanyAdmin() && $user->company_id === $model->company_id;
    }
}
