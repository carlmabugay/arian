<?php

namespace App\Filament\Resources\Locations\Tables;

use App\Enums\LocationType;
use App\Models\Location;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Locations')
            ->description('Manage your company locations here.')
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Add new')
                    ->size(Size::Small)
                    ->slideOver()
                    ->modalHeading('Location Details')
                    ->modalWidth(Width::Medium)
                    ->authorize('create'),
            ])
            ->columns([
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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->multiple()
                    ->options(LocationType::class),
                TrashedFilter::make(),
            ])
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AfterContent)
            ->recordActions(
                ActionGroup::make([
                    EditAction::make()->authorize('update', Location::class),
                ])
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('Delete selected')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $user = auth()->user();

                            $blocked = $records->filter(
                                fn ($location) => $user->cannot('delete', $location)
                            );

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Bulk delete blocked')
                                    ->body('One or more selected location have active assets.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $records->each->delete();
                            Notification::make()
                                ->title('Location deleted')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('forceDelete')
                        ->label('Force delete selected')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Permanent delete locations')
                        ->modalDescription('This action is irreversible.')
                        ->visible(fn () => auth()->user()->can('forceDeleteAny', Location::class))
                        ->action(function (Collection $records) {
                            $user = auth()->user();

                            $blocked = $records->filter(
                                fn (Location $location) => $user->cannot('forceDelete', $location)
                            );

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Permanent delete blocked')
                                    ->body('One or more selected location still have related records.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $count = $records->count();
                            $records->each->forceDelete();

                            Notification::make()
                                ->title('Location permanently deleted')
                                ->body("{$count} location".($count > 1 ? 's were' : ' was').' permanently deleted.')
                                ->success()
                                ->send();

                        }),
                    BulkAction::make('restore')
                        ->label('Restore selected')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore locations')
                        ->modalDescription('The selected location will be restored.')
                        ->visible(fn () => auth()->user()->can('restoreAny', Location::class))
                        ->action(function (Collection $records) {
                            $user = auth()->user();

                            $blocked = $records->filter(
                                fn (Location $location) => $user->cannot('restore', $location)
                            );

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Restore blocked')
                                    ->body('One or more selected locations cannot be restored.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $count = $records->count();
                            $records->each->restore();

                            Notification::make()
                                ->title('Locations restored')
                                ->body("{$count} location".($count > 1 ? 's were' : ' was').' restored successfully.')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}
