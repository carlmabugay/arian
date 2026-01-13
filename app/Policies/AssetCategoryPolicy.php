<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AssetCategory;
use App\Models\User;

class AssetCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::CompanyAdmin,
        ]);
    }

    public function view(User $user, AssetCategory $category): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [
            UserRole::SuperAdmin,
            UserRole::CompanyAdmin,
        ]);
    }

    public function update(User $user, AssetCategory $category): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, AssetCategory $category): bool
    {
        return $user->isSuperAdmin() && ! $category->hasAssets();
    }

    public function restore(User $user, AssetCategory $category): bool
    {
        return $user->isSuperAdmin();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, AssetCategory $category): bool
    {
        return $user->isSuperAdmin() && ! $category->hasAssets();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
