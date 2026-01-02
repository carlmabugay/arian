<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected static ?string $relatedResource = AssetResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Company Assets')
            ->description(sprintf("Manage %s assets here.", $this->ownerRecord->name))
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus)
                    ->label('Add new')
                    ->size(Size::Small)
                    ->slideOver()
                    ->modalHeading('Add New Asset')
                    ->modalWidth('md')
            ])
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->searchable(),
                TextColumn::make('assignedUser.name')
                    ->label('Assigned to'),
                TextColumn::make('condition')
                    ->badge(),

            ]);
    }
}
