<?php

namespace App\Filament\Resources\Assets\Tables;

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

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Assets')
            ->description('Manage your assets here.')
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Add New')
            ])
            ->columns([
                TextColumn::make('company.name')
                    ->searchable(),
                TextColumn::make('category.name'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('condition')
                    ->badge(),
                TextColumn::make('asset_tag')
                    ->searchable(),
                TextColumn::make('serial_number')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('purchased_at')
                    ->label('Purchased Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('purchase_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->searchable(),
                TextColumn::make('assignedUser.name')
                    ->label('Assigned To')
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions(
                ActionGroup::make([
                    EditAction::make()
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
