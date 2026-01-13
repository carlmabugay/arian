<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Enums\SystemAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('System Logs')
            ->description('Manage your system activity logs here.')
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('user.name')->label('User')->placeholder('System'),
                TextColumn::make('action')->badge(),
                TextColumn::make('event')->badge(),
                TextColumn::make('auditable_type')->label('Model'),
                TextColumn::make('auditable_id')->label('ID'),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options(SystemAction::values()),

                SelectFilter::make('event')
                    ->options([
                        'asset_assigned' => 'Asset Assigned',
                        'asset_returned' => 'Asset Returned',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->striped();
    }
}
