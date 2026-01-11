<?php

namespace App\Filament\Resources\Locations\Tables;

use App\Enums\LocationType;
use App\Enums\SystemAction;
use App\Filament\Resources\Locations\Schemas\LocationForm;
use App\Filament\Traits\ConfigureSystemAction;
use App\Filament\Traits\HasBulkActions;
use App\Filament\Traits\HasNotificationMessage;
use App\Helpers\SystemMessageHelper;
use App\Models\Location;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class LocationsTable
{
    use ConfigureSystemAction, HasBulkActions, HasNotificationMessage;

    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Locations')
            ->description('Manage your company locations here.')
            ->headerActions(self::headerActions())
            ->columns(self::columns())
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Search location by name and company name...')
            ->filters(self::filters())
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions(self::recordActions())
            ->toolbarActions(self::bulkActions())
            ->striped();
    }

    protected static function headerActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus)
                ->label('Add new')
                ->size(Size::Small)
                ->slideOver()
                ->modalHeading('Location Details')
                ->modalWidth(Width::Medium)
                ->schema(LocationForm::form())
                ->tap(fn (CreateAction $action) => static::configureAction($action, SystemAction::Create, 'Location'))
                ->authorize('create'),
        ];
    }

    protected static function columns(): array
    {
        return [
            TextColumn::make('name')
                ->searchable(),

            TextColumn::make('type')
                ->badge(),

            TextColumn::make('company.name')
                ->searchable(),

            TextColumn::make('assets_count')
                ->label('Total assets')
                ->counts('assets'),

            TextColumn::make('created_at')
                ->label('Creation Date')
                ->date()
                ->sortable(),
        ];
    }

    protected static function filters(): array
    {

        return [
            SelectFilter::make('type')
                ->multiple()
                ->options(LocationType::class),

            TrashedFilter::make(),
        ];
    }

    protected static function recordActions(): ActionGroup
    {
        return ActionGroup::make([
            EditAction::make()->authorize('update', Location::class),
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

    protected static function bulkDelete(): BulkAction
    {
        return BulkAction::make('delete')
            ->label('Trash selected')
            ->color('warning')
            ->icon(Heroicon::OutlinedTrash)
            ->requiresConfirmation()
            ->modalHeading(
                SystemMessageHelper::confirmHeading(
                    SystemAction::Delete,
                    'locations'
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Delete,
                    'locations'
                )
            )
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    $records,
                    'delete',
                    'Bulk trash blocked',
                    'One or more selected location cannot be trashed.'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->delete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Delete,
                            'Location'
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Delete,
                            $count,
                            'location'
                        )
                    )
                    ->success()
                    ->send();
            });
    }

    protected static function bulkForceDelete(): BulkAction
    {
        return BulkAction::make('forceDelete')
            ->label('Force delete selected')
            ->color('danger')
            ->icon(Heroicon::OutlinedXMark)
            ->requiresConfirmation()
            ->modalHeading(
                SystemMessageHelper::confirmHeading(
                    SystemAction::ForceDelete,
                    'locations'
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::ForceDelete,
                    'locations'
                )
            )
            ->visible(fn () => auth()->user()->can('forceDeleteAny', Location::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    $records,
                    'forceDelete',
                    'Permanent delete blocked',
                    'One or more selected locations cannot be permanently deleted.'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->forceDelete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::ForceDelete,
                            'Location'
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::ForceDelete,
                            $count,
                            'location'
                        )
                    )
                    ->success()
                    ->send();

            });
    }

    protected static function bulkRestore(): BulkAction
    {
        return BulkAction::make('restore')
            ->label('Restore selected')
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(
                SystemMessageHelper::confirmHeading(
                    SystemAction::Restore,
                    'locations'
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Restore,
                    'locations'
                )
            )
            ->visible(fn () => auth()->user()->can('restoreAny', Location::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    $records,
                    'restore',
                    'Restore blocked',
                    'One or more selected locations cannot be restored.'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->restore();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Restore,
                            'Location'
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Restore,
                            $count,
                            'location'
                        )
                    )
                    ->success()
                    ->send();
            });
    }
}
