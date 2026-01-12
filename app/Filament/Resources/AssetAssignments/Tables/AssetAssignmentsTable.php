<?php

namespace App\Filament\Resources\AssetAssignments\Tables;

use App\Enums\SystemAction;
use App\Filament\Traits\ConfigureSystemAction;
use App\Filament\Traits\HasBulkActions;
use App\Helpers\SystemMessageHelper;
use App\Models\AssetAssignment;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AssetAssignmentsTable
{
    use ConfigureSystemAction, HasBulkActions;

    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Asset Assignments')
            ->description('Manage asset assignments here.')
            ->headerActions(self::headerActions())
            ->columns(self::columns())
            ->defaultSort('assigned_at', 'desc')
            ->filters(self::filters())
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions(self::recordActions())
            ->toolbarActions(self::bulkActions())
            ->striped();
    }

    public static function headerActions(): array
    {
        return [
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
                        'Asset assignment',
                    )
                ),
        ];
    }

    public static function columns(): array
    {
        return [
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
        ];
    }

    public static function filters(): array
    {
        return [
            Filter::make('active')
                ->label('Active assignments')
                ->query(fn (Builder $query) => $query->whereNull('returned_at'))
                ->toggle(),

            Filter::make('returned')
                ->label('Returned')
                ->query(fn (Builder $query) => $query->whereNotNull('returned_at'))
                ->toggle(),
        ];
    }

    public static function recordActions(): ActionGroup
    {
        return ActionGroup::make([
            EditAction::make()
                ->slideOver()
                ->modalWidth(Width::Medium)
                ->authorize('update', AssetAssignment::class)
                ->successNotificationTitle(
                    SystemMessageHelper::successTitle(
                        SystemAction::Update,
                        'Assignment',
                    )
                ),
        ]);
    }

    public static function bulkActions(): array
    {
        return [
            BulkActionGroup::make([
                self::bulkDelete(),
                self::bulkForceDelete(),
                self::bulkRestore(),
            ]),
        ];
    }

    public static function bulkDelete(): BulkAction
    {
        return BulkAction::make('delete')
            ->label('Trash selected')
            ->color('warning')
            ->icon(Heroicon::OutlinedTrash)
            ->requiresConfirmation()
            ->modalHeading(
                SystemMessageHelper::confirmHeading(
                    SystemAction::Delete,
                    'asset assignments',
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Delete,
                    'asset assignments'
                )
            )
            ->visible(fn () => auth()->user()->can('deleteAny', AssetAssignment::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    records: $records,
                    ability: 'delete',
                    systemAction: SystemAction::Delete,
                    noun: 'asset assignments'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->delete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Delete,
                            'Asset assignments',
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Delete,
                            $count,
                            'assignment'
                        )
                    )
                    ->success()
                    ->send();
            });
    }

    public static function bulkForceDelete(): BulkAction
    {
        return BulkAction::make('forceDelete')
            ->label('Force delete selected')
            ->color('danger')
            ->icon(Heroicon::OutlinedXMark)
            ->requiresConfirmation()
            ->modalHeading(
                SystemMessageHelper::confirmHeading(
                    SystemAction::ForceDelete,
                    'asset assignments',
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::ForceDelete,
                    'asset assignments'
                )
            )
            ->visible(fn () => auth()->user()->can('forceDeleteAny', AssetAssignment::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    records: $records,
                    ability: 'forceDelete',
                    systemAction: SystemAction::ForceDelete,
                    noun: 'asset assignments'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->forceDelete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::ForceDelete,
                            'Asset assignments',
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::ForceDelete,
                            $count,
                            'asset assignment'
                        )
                    )
                    ->success()
                    ->send();

            });
    }

    public static function bulkRestore(): BulkAction
    {
        return BulkAction::make('restore')
            ->label('Restore selected')
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(
                SystemMessageHelper::confirmHeading(
                    SystemAction::Restore,
                    'asset assignments',
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Restore,
                    'asset assignments'
                )
            )
            ->visible(fn () => auth()->user()->can('restoreAny', AssetAssignment::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    records: $records,
                    ability: 'restore',
                    systemAction: SystemAction::Restore,
                    noun: 'asset assignments'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->restore();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Restore,
                            'Asset assignments',
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Restore,
                            $count,
                            'asset assignment'
                        )
                    )
                    ->success()
                    ->send();
            });
    }
}
