<?php

namespace App\Filament\Resources\Companies\Tables;

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
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Companies')
            ->description('Manage your companies here.')
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus)
                    ->label('Add new')
                    ->size(Size::Small)
                    ->slideOver()
                    ->modalHeading('Add new company')
                    ->modalWidth(Width::Medium)
                    ->authorize('create'),
            ])
            ->columns([
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
            ])
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Search...')
            ->filters([
                Filter::make('with_users')
                    ->query(fn (Builder $query): Builder => $query->whereHas('users'))
                    ->toggle(),
                Filter::make('with_assets')
                    ->query(fn (Builder $query): Builder => $query->whereHas('assets'))
                    ->toggle(),
                TrashedFilter::make(),
            ])
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions(
                ActionGroup::make([
                    EditAction::make()
                        ->authorize('update', Company::class),
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
                                fn ($company) => $user->cannot('delete', $company)
                            );

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Bulk delete blocked')
                                    ->body('One or more selected companies have active users and assets.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $records->each->delete();
                        }),
                    BulkAction::make('forceDelete')
                        ->label('Force delete selected')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Permanent delete companies')
                        ->modalDescription('This action is irreversible.')
                        ->visible(fn () => auth()->user()->can('forceDeleteAny', Company::class))
                        ->action(function (Collection $records) {
                            $user = auth()->user();

                            $blocked = $records->filter(
                                fn (Company $company) => $user->cannot('forceDelete', $company)
                            );

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Permanent delete blocked')
                                    ->body('One or more selected companies still have related records.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $count = $records->count();
                            $records->each->forceDelete();

                            Notification::make()
                                ->title('Companies permanently deleted')
                                ->body("{$count} company".($count > 1 ? 'ies were' : ' was').' permanently deleted.')
                                ->success()
                                ->send();

                        }),
                    BulkAction::make('restore')
                        ->label('Restore selected')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore companies')
                        ->modalDescription('The selected companies will be restored.')
                        ->visible(fn () => auth()->user()->can('restoreAny', Company::class))
                        ->action(function (Collection $records) {
                            $user = auth()->user();

                            $blocked = $records->filter(
                                fn (Company $company) => $user->cannot('restore', $company)
                            );

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Restore blocked')
                                    ->body('One or more selected companies cannot be restored.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $count = $records->count();
                            $records->each->restore();

                            Notification::make()
                                ->title('Companies restored')
                                ->body("{$count} company".($count > 1 ? 'ies were' : ' was').' restored successfully.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->striped();
    }
}
