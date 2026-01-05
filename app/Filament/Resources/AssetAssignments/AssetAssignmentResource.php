<?php

namespace App\Filament\Resources\AssetAssignments;

use App\Filament\Resources\AssetAssignments\Pages\ListAssetAssignments;
use App\Filament\Resources\AssetAssignments\Schemas\AssetAssignmentForm;
use App\Filament\Resources\AssetAssignments\Tables\AssetAssignmentsTable;
use App\Models\AssetAssignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AssetAssignmentResource extends Resource
{
    protected static ?string $model = AssetAssignment::class;

    protected static string|UnitEnum|null $navigationGroup = 'Asset Management';

    protected static ?string $navigationLabel = 'Assignments';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return AssetAssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetAssignmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetAssignments::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
