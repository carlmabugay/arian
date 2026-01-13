<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class AuditLogPolicy
{
    public static function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::CompanyAdmin,
        ]);
    }
}
