<?php

namespace App\Filament\Resources\AssetCategories\RelationManagers;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Contracts\Support\Htmlable;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected static ?string $relatedResource = AssetResource::class;

    protected function getTableHeading(): string|Htmlable|null
    {
        return sprintf('%s Assets', $this->ownerRecord->name);
    }

    protected function getTableDescription(): string|Htmlable|null
    {
        return sprintf('Manage %s assets here.', $this->ownerRecord->name);
    }
}
