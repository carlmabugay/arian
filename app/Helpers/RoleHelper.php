<?php

namespace App\Helpers;

use App\Enums\UserRole;
use Illuminate\Support\Str;

class RoleHelper
{
    public static function all(): array
    {
        return collect(UserRole::values())
            ->mapWithKeys(fn (string $role) => [
                $role => Str::of($role)->replace('_', ' ')->title(),
            ])
            ->toArray();
    }

    public static function forCompanyAdmin(): array
    {
        return collect(UserRole::values())
            ->reject(fn (string $role) => $role === UserRole::SuperAdmin->value)
            ->mapWithKeys(fn (string $role) => [
                $role => Str::of($role)->replace('_', ' ')->title(),
            ])
            ->toArray();
    }
}
