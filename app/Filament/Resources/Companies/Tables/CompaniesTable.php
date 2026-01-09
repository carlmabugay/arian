<?php

namespace App\Filament\Resources\Companies\Tables;

use App\Filament\Resources\Companies\Schemas\CompanyForm;
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
use Illuminate\Support\Str;

class CompaniesTable
{
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
            ->action(function (Collection $records) {

                $success = self::guardBulkAction(
                    $records,
                    'delete',
                    'Trash blocked',
                    'One or more selected companies cannot be trashed.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->delete();

                Notification::make()
                    ->title('Companies trashed')
                    ->body(self::notificationMessage(
                        count: $count,
                        action: 'trashed successfully',
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
            ->modalHeading('Permanent delete companies')
            ->modalDescription('This action is irreversible.')
            ->visible(fn () => auth()->user()->can('forceDeleteAny', Company::class))
            ->action(function (Collection $records) {

                $success = self::guardBulkAction(
                    $records,
                    'forceDelete',
                    'Permanent delete blocked',
                    'One or more selected companies cannot be permanently deleted.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->forceDelete();

                Notification::make()
                    ->title('Companies permanently deleted')
                    ->body(self::notificationMessage(
                        count: $count,
                        action: 'permanently deleted',
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
            ->modalHeading('Restore companies')
            ->modalDescription('The selected companies will be restored.')
            ->visible(fn () => auth()->user()->can('restoreAny', Company::class))
            ->action(function (Collection $records) {

                $success = self::guardBulkAction(
                    $records,
                    'restore',
                    'Restore blocked',
                    'One or more selected companies cannot be restored.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->restore();

                Notification::make()
                    ->title('Companies restored')
                    ->body(self::notificationMessage(
                        count: $count,
                        action: 'restored successfully',
                    ))
                    ->success()
                    ->send();
            });
    }

    protected static function guardBulkAction(Collection $records, string $ability, string $title, string $message): bool
    {
        $user = auth()->user();

        $blocked = $records->filter(
            fn ($record) => $user->cannot($ability, $record)
        );

        if ($blocked->isNotEmpty()) {
            Notification::make()
                ->title($title)
                ->body($message)
                ->danger()
                ->send();
        }

        return $blocked->isEmpty();
    }

    protected static function notificationMessage(int $count, string $action, string $noun = 'company'): string
    {
        $verb = $count === 1 ? 'was' : 'were';

        return sprintf(
            '%d %s %s %s.',
            $count,
            Str::plural($noun, $count),
            $verb,
            $action,
        );
    }
}
