<?php

namespace App\Filament\Resources\AssetAssignments\Tables;

use App\Enums\SystemAction;
use App\Helpers\SystemMessageHelper;
use App\Models\AssetAssignment;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssetAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Asset Assignments')
            ->description('Manage asset assignments here.')
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus)
                    ->label('Add new')
                    ->size(Size::Small)
                    ->slideOver()
                    ->modalHeading('Asset Assignment Details')
                    ->modalWidth(Width::Medium)
                    ->authorize('create', AssetAssignment::class)
                    ->mutateDataUsing(function (array $data): array {
                        $data['assigned_by'] = auth()->id();
                        $data['assigned_at'] ??= now();

                        return $data;
                    })
                    ->successNotificationTitle(
                        SystemMessageHelper::successTitle(
                            SystemAction::Create,
                            'Assignment',
                        )
                    ),
            ])
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label('Asset')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Assigned To')
                    ->searchable(),

                TextColumn::make('assigned_at')
                    ->date()
                    ->sortable(),

                TextColumn::make('returned_at')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->state(fn ($record) => $record->returned_at ? 'Returned' : 'Active'
                    )
                    ->badge()
                    ->colors([
                        'success' => 'Active',
                        'gray' => 'Returned',
                    ]),
            ])
            ->defaultSort('assigned_at', 'desc')
            ->filters([
                Filter::make('active')
                    ->label('Active assignments')
                    ->query(fn (Builder $query) => $query->whereNull('returned_at'))
                    ->toggle(),

                Filter::make('returned')
                    ->label('Returned')
                    ->query(fn (Builder $query) => $query->whereNotNull('returned_at'))
                    ->toggle(),
            ])
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions(
                ActionGroup::make([
                    EditAction::make()
                        ->slideOver()
                        ->modalWidth(Width::Medium)
                        ->successNotificationTitle(
                            SystemMessageHelper::successTitle(
                                SystemAction::Update,
                                'Assignment',
                            )
                        ),
                ])
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->striped();
    }
}
