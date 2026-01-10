<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $relatedResource = UserResource::class;

    protected function modifyQueryWithActiveTab(Builder $query): Builder
    {
        $user = auth()->user();

        return $query->whereNot('id', $user->id);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return sprintf('%s Users', $this->ownerRecord->name);
    }

    protected function getTableDescription(): string|Htmlable|null
    {
        return sprintf('Manage %s users here.', $this->ownerRecord->name);
    }
}
