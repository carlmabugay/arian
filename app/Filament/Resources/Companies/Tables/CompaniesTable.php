<?php

namespace App\Filament\Resources\Companies\Tables;

use App\Enums\SystemAction;
use App\Filament\Resources\Companies\Schemas\CompanyForm;
use App\Filament\Traits\ConfigureSystemAction;
use App\Filament\Traits\HasBulkActions;
use App\Helpers\SystemMessageHelper;
use App\Models\Company;
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

class CompaniesTable
{
    use ConfigureSystemAction, HasBulkActions;

    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Companies')
            ->description('Manage your companies here.')
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

    protected static function headerActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus)
                ->label('Add new')
                ->size(Size::Small)
                ->slideOver()
                ->modalHeading('Company Details')
                ->modalWidth(Width::Medium)
                ->schema(CompanyForm::form())
                ->tap(fn (CreateAction $action) => static::configureAction($action, SystemAction::Create, 'Company'))
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

            TextColumn::make('users_count')
                ->label('Total users')
                ->counts('users'),

            TextColumn::make('assets_count')
                ->label('Total assets')
                ->counts('assets'),

            TextColumn::make('created_at')
                ->label('Created at')
                ->date()
                ->sortable(),
        ];
    }

    protected static function filters(): array
    {
        return [
            Filter::make('with_users')
                ->query(fn (Builder $query): Builder => $query->whereHas('users'))
                ->toggle(),

            Filter::make('with_assets')
                ->query(fn (Builder $query): Builder => $query->whereHas('assets'))
                ->toggle(),

            TrashedFilter::make(),
        ];
    }

    protected static function recordActions(): ActionGroup
    {
        return ActionGroup::make([
            EditAction::make()
                ->authorize('update', Company::class),
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
                    'companies'
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Delete,
                    'companies'
                )
            )
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    $records,
                    'delete',
                    'Trash blocked',
                    'One or more selected companies cannot be trashed.'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->delete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Delete,
                            'Company'
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Delete,
                            $count,
                            'company'
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
                    'companies'
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::ForceDelete,
                    'companies'
                )
            )
            ->visible(fn () => auth()->user()->can('forceDeleteAny', Company::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    $records,
                    'forceDelete',
                    'Permanent delete blocked',
                    'One or more selected companies cannot be permanently deleted.'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->forceDelete();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::ForceDelete,
                            'Company'
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::ForceDelete,
                            $count,
                            'company'
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
                    'companies'
                )
            )
            ->modalDescription(
                SystemMessageHelper::confirmBody(
                    SystemAction::Restore,
                    'companies'
                )
            )
            ->visible(fn () => auth()->user()->can('restoreAny', Company::class))
            ->action(function (Collection $records) {

                if (! static::guardBulkAction(
                    $records,
                    'restore',
                    'Restore blocked',
                    'One or more selected companies cannot be restored.'
                )) {
                    return;
                }

                $count = $records->count();
                $records->each->restore();

                Notification::make()
                    ->title(
                        SystemMessageHelper::successTitle(
                            SystemAction::Restore,
                            'Company'
                        )
                    )
                    ->body(
                        SystemMessageHelper::successBody(
                            SystemAction::Restore,
                            $count,
                            'company'
                        )
                    )
                    ->success()
                    ->send();
            });
    }
}
