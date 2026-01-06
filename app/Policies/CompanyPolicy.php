<?php

namespace App\Policies;

use App\Enums\UserRole;
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
}
