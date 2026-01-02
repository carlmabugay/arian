<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use App\Filament\Resources\AssetAssignments\AssetAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $relatedResource = AssetAssignmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Assignment History')
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Add New'),
            ]);
    }
}
