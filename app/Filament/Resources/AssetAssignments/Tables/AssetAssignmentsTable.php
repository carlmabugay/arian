<?php

namespace App\Filament\Resources\AssetAssignments\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssetAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Asset Assigments')
            ->description('Manage asset assignments here.')
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Add New'),
            ])
            ->columns([
                TextColumn::make('asset.name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Assigned To')
                    ->searchable(),
                TextColumn::make('assignedBy.name')
                    ->label('Assigned By'),
                TextColumn::make('assigned_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('assigned_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions(
                ActionGroup::make([
                    EditAction::make(),
                ])
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
