<?php

namespace App\Filament\Resources\Locations\Tables;

use App\Enums\LocationType;
use App\Filament\Resources\Locations\Schemas\LocationForm;
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
use Illuminate\Support\Str;

class LocationsTable
{
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
            ->action(function (Collection $records) {

                $success = self::guardBulkAction(
                    $records,
                    'delete',
                    'Bulk trash blocked',
                    'One or more selected location cannot be trashed.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->delete();

                Notification::make()
                    ->title('Locations trashed')
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
            ->modalHeading('Permanent delete locations')
            ->modalDescription('This action is irreversible.')
            ->visible(fn () => auth()->user()->can('forceDeleteAny', Location::class))
            ->action(function (Collection $records) {

                $success = self::guardBulkAction(
                    $records,
                    'forceDelete',
                    'Permanent delete blocked',
                    'One or more selected locations cannot be permanently deleted.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->forceDelete();

                Notification::make()
                    ->title('Location permanently deleted')
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
            ->modalHeading('Restore locations')
            ->modalDescription('The selected location will be restored.')
            ->visible(fn () => auth()->user()->can('restoreAny', Location::class))
            ->action(function (Collection $records) {
                $success = self::guardBulkAction(
                    $records,
                    'restore',
                    'Restore blocked',
                    'One or more selected locations cannot be restored.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->restore();

                Notification::make()
                    ->title('Locations restored')
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

    protected static function notificationMessage(int $count, string $action, string $noun = 'location'): string
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
