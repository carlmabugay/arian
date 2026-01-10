<?php

namespace App\Filament\Resources\AssetCategories\Tables;

use App\Filament\Resources\AssetCategories\Schemas\AssetCategoryForm;
use App\Filament\Traits\HasBulkActions;
use App\Filament\Traits\HasNotificationMessage;
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
    use HasBulkActions, HasNotificationMessage;

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
                ->modalHeading('Category Details')
                ->modalWidth(Width::Medium)
                ->schema(AssetCategoryForm::form())
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
                ->label('Created')
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
            ->action(function (Collection $records) {

                $success = static::guardBulkAction(
                    $records,
                    'delete',
                    'Bulk trash blocked',
                    'One or more selected category cannot be trashed.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->delete();

                Notification::make()
                    ->title('Asset categories trashed')
                    ->body(static::notificationMessage(
                        count: $count,
                        action: 'trashed successfully',
                        noun: 'category',
                    ))
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
            ->modalHeading('Permanent delete categories')
            ->modalDescription('This action is irreversible.')
            ->visible(fn () => auth()->user()->can('forceDeleteAny', AssetCategory::class))
            ->action(function (Collection $records) {

                $success = static::guardBulkAction(
                    $records,
                    'forceDelete',
                    'Permanent delete blocked',
                    'One or more selected categories cannot be permanently deleted.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->forceDelete();

                Notification::make()
                    ->title('Category permanently deleted')
                    ->body(static::notificationMessage(
                        count: $count,
                        action: 'permanently deleted',
                        noun: 'category',
                    ))
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
            ->modalHeading('Restore categories')
            ->modalDescription('The selected category will be restored.')
            ->visible(fn () => auth()->user()->can('restoreAny', AssetCategory::class))
            ->action(function (Collection $records) {
                $success = static::guardBulkAction(
                    $records,
                    'restore',
                    'Restore blocked',
                    'One or more selected categories cannot be restored.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->restore();

                Notification::make()
                    ->title('Categories restored')
                    ->body(static::notificationMessage(
                        count: $count,
                        action: 'restored successfully',
                        noun: 'category',
                    ))
                    ->success()
                    ->send();
            });
    }
}
