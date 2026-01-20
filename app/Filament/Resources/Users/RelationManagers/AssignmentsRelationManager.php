<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\AssetAssignments\AssetAssignmentResource;
use Filament\Resources\RelationManagers\RelationManager;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assetAssignments';

    protected static ?string $relatedResource = AssetAssignmentResource::class;
}
