<?php

namespace App\Filament\Resources\AssetCategories\Tables;

use App\Enums\SystemAction;
use App\Filament\Resources\AssetCategories\Schemas\AssetCategoryForm;
use App\Filament\Traits\ConfigureSystemAction;
use App\Filament\Traits\HasBulkActions;
use App\Helpers\SystemMessageHelper;
use App\Models\AssetCategory;
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
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AssetCategoriesTable
{
    use ConfigureSystemAction, HasBulkActions;

    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Categories')
            ->description('Manage your asset categories here.')
            ->headerActions(self::headerActions())
            ->columns(self::columns())
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Search...')
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
                ->label('Add New')
                ->size(Size::Small)
                ->slideOver()
                ->modalHeading('Asset Category Details')
                ->modalWidth(Width::Medium)
                ->schema(AssetCategoryForm::form())
                ->tap(fn (CreateAction $action) => static::configureAction($action, SystemAction::Create, 'Asset category'))
                ->authorize('create'),
        ];
    }

    protected static function columns(): array
    {
        return [
            TextColumn::make('name')
                ->searchable()
                ->sortable(),

            TextColumn::make('code')
                ->searchable(),

            TextColumn::make('assets_count')
                ->label('Total assets')
                ->counts('assets'),

            TextColumn::make('created_at')
                ->label('Created on')
                ->date()
                ->sortable(),
        ];
    }

    protected static function filters(): array
    {
        return [
            Filter::make('with_assets')
                ->query(fn (Builder $query) => $query->whereHas('assets'))
                ->toggle(),

            TrashedFilter::make(),
        ];
    }

    protected static function recordActions(): ActionGroup
    {
        return ActionGroup::make([
            EditAction::make()->authorize('update', AssetCategory::class),
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
                    'asset categories',
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Delete,
                    'asset categories'
                )
            )
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    records: $records,
                    ability: 'delete',
                    systemAction: SystemAction::Delete,
                    noun: 'asset categories'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->delete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Delete,
                            'Asset categories',
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Delete,
                            $count,
                            'asset category'
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
                    'asset categories',
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::ForceDelete,
                    'asset categories'
                )
            )
            ->visible(fn () => auth()->user()->can('forceDeleteAny', AssetCategory::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    records: $records,
                    ability: 'forceDelete',
                    systemAction: SystemAction::ForceDelete,
                    noun: 'asset categories'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->forceDelete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::ForceDelete,
                            'Asset categories',
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::ForceDelete,
                            $count,
                            'asset category'
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
                    'asset categories',
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Restore,
                    'asset categories'
                )
            )
            ->visible(fn () => auth()->user()->can('restoreAny', AssetCategory::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    records: $records,
                    ability: 'restore',
                    systemAction: SystemAction::Restore,
                    noun: 'asset categories'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->restore();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Restore,
                            'Asset categories',
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Restore,
                            $count,
                            'asset category'
                        )
                    )
                    ->success()
                    ->send();
            });
    }
}
