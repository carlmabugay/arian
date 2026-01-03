<?php

namespace App\Filament\Resources\AssetAssignments\Pages;

use App\Filament\Resources\AssetAssignments\AssetAssignmentResource;
use Filament\Resources\Pages\ListRecords;

class ListAssetAssignments extends ListRecords
{
    protected static string $resource = AssetAssignmentResource::class;

    protected ?string $heading = '';
}
