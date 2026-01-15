<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\SystemAction;
use App\Filament\Traits\ConfigureSystemAction;
use App\Filament\Traits\HasBulkActions;
use App\Helpers\SystemMessageHelper;
use App\Livewire\AssetAssignmentHistory;
use App\Models\Asset;
use App\Models\Location;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Livewire;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class AssetsTable
{
    use ConfigureSystemAction, HasBulkActions;

    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Assets')
            ->description('Manage your assets here.')
            ->headerActions(self::headerActions())
            ->emptyStateActions(self::headerActions())
            ->columns(self::columns())
            ->defaultSort('created_at', 'desc')
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
                ->authorize('create'),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Asset')
                ->searchable()
                ->sortable(),

            TextColumn::make('asset_tag')
                ->label('Asset Tag')
                ->searchable()
                ->copyable()
                ->toggleable(),

            TextColumn::make('category.name')
                ->label('Category')
                ->sortable(),

            TextColumn::make('location.name')
                ->label('Location')
                ->sortable(),

            TextColumn::make('assignedUser.name')
                ->label('Assigned To')
                ->placeholder('—')
                ->toggleable(),

            TextColumn::make('status')
                ->label('Status')
                ->badge(),

            TextColumn::make('condition')
                ->label('Condition')
                ->badge(),

            TextColumn::make('purchase_price')
                ->label('Price')
                ->money('USD')
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('purchased_at')
                ->label('Purchased')
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('company.name')
                ->label('Company')
                ->visible(auth()->user()->isSuperAdmin())
                ->hiddenOn('relation')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Created')
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function filters(): array
    {
        return [

            SelectFilter::make('status')
                ->options(AssetStatus::class)
                ->native(false),

            SelectFilter::make('condition')
                ->options(AssetCondition::class)
                ->native(false),

            SelectFilter::make('category')
                ->relationship('category', 'name')
                ->native(false),

            SelectFilter::make('location')
                ->relationship('location', 'name')
                ->options(Location::query()->pluck('name', 'id'))
                ->native(false),

            TrashedFilter::make(),
        ];
    }

    public static function recordActions(): ActionGroup
    {
        return ActionGroup::make([

            ViewAction::make()
                ->label('View Assignment History')
                ->modal()
                ->modalHeading(fn (Asset $record) => "Assignment History — {$record->name}")
                ->modalWidth(Width::FourExtraLarge)
                ->schema(fn (Asset $record) => [
                    Livewire::make(AssetAssignmentHistory::class, [
                        'asset' => $record,
                    ]),
                ]),
            EditAction::make()->authorize('update', Asset::class),
        ]);
    }

    protected static function bulkActions(): array
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
                    'assets',
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Delete,
                    'assets'
                )
            )
            ->visible(fn () => auth()->user()->can('deleteAny', Asset::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    records: $records,
                    ability: 'delete',
                    systemAction: SystemAction::Delete,
                    noun: 'assets'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->delete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Delete,
                            'Asset',
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Delete,
                            $count,
                            'asset'
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
                    'assets',
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::ForceDelete,
                    'assets'
                )
            )
            ->visible(fn () => auth()->user()->can('forceDeleteAny', Asset::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    records: $records,
                    ability: 'forceDelete',
                    systemAction: SystemAction::ForceDelete,
                    noun: 'assets'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->forceDelete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::ForceDelete,
                            'Assets',
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::ForceDelete,
                            $count,
                            'asset'
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
                    'assets',
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Restore,
                    'assets'
                )
            )
            ->visible(fn () => auth()->user()->can('restoreAny', Asset::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    records: $records,
                    ability: 'restore',
                    systemAction: SystemAction::Restore,
                    noun: 'assets'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->restore();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Restore,
                            'Assets',
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Restore,
                            $count,
                            'asset'
                        )
                    )
                    ->success()
                    ->send();
            });
    }
}
