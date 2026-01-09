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

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::CompanyAdmin]);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->canManageUser($user, $model);
    }

    public function restoreAny(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function restore(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    protected function canManageUser(User $user, User $model): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        if ($user->role === UserRole::CompanyAdmin) {
            return $user->company_id === $model->company_id;
        }

        return false;
    }
}
