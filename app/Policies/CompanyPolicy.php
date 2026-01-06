<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return $user->role == UserRole::SuperAdmin;
    }
}
