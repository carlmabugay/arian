<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Filament\Resources\Companies\RelationManagers\UsersRelationManager;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Traits\HasBulkActions;
use App\Filament\Traits\HasNotificationMessage;
use App\Helpers\RoleHelper;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class UsersTable
{
    use HasBulkActions, HasNotificationMessage;

    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Users')
            ->description('Manage users here.')
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
                ->label('Add new')
                ->size(Size::Small)
                ->slideOver()
                ->modalHeading('User Details')
                ->modalWidth(Width::Medium)
                ->schema(UserForm::form())
                ->authorize('create'),
        ];
    }

    public static function columns(): array
    {
        return [

            TextColumn::make('name')
                ->searchable(),

            TextColumn::make('email')
                ->label('Email address')
                ->copyable()
                ->searchable(),

            TextColumn::make('company.name')
                ->label('Company')
                ->visible(auth()->user()->role === UserRole::SuperAdmin)
                ->hiddenOn(UsersRelationManager::class),

            TextColumn::make('role')
                ->badge(),

            IconColumn::make('is_active')
                ->label('Active')
                ->boolean(),

            TextColumn::make('created_at')
                ->label('Created at')
                ->date()
                ->sortable(),
        ];
    }

    public static function filters(): array
    {
        return [
            SelectFilter::make('role')
                ->options(
                    auth()->user()->role === UserRole::SuperAdmin
                        ? RoleHelper::all()
                        : RoleHelper::forCompanyAdmin()
                ),

            SelectFilter::make('company_id')
                ->label('Company')
                ->relationship('company', 'name')
                ->visible(fn () => auth()->user()->role === UserRole::SuperAdmin),

            TrashedFilter::make(),
        ];
    }

    public static function recordActions(): ActionGroup
    {
        return ActionGroup::make([
            EditAction::make()->authorize('update', User::class),
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
            ->action(function (Collection $records) {

                $success = static::guardBulkAction(
                    $records,
                    'delete',
                    'Trash blocked',
                    'One or more selected users cannot be trashed.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->delete();

                Notification::make()
                    ->title('Users trashed')
                    ->body(static::notificationMessage(
                        count: $count,
                        action: 'trashed successfully',
                        noun: 'user',
                    ))
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
            ->modalHeading('Permanent delete users')
            ->modalDescription('This action is irreversible.')
            ->visible(fn () => auth()->user()->can('forceDeleteAny', User::class))
            ->action(function (Collection $records) {

                $success = static::guardBulkAction(
                    $records,
                    'forceDelete',
                    'Permanent delete blocked',
                    'One or more selected users cannot be permanently deleted.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->forceDelete();

                Notification::make()
                    ->title('Users permanently deleted')
                    ->body(static::notificationMessage(
                        count: $count,
                        action: 'permanently deleted',
                        noun: 'user',
                    ))
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
            ->modalHeading('Restore users')
            ->modalDescription('The selected users will be restored.')
            ->visible(fn () => auth()->user()->can('restoreAny', User::class))
            ->action(function (Collection $records) {

                $success = static::guardBulkAction(
                    $records,
                    'restore',
                    'Restore blocked',
                    'One or more selected users cannot be restored.'
                );

                if (! $success) {
                    return;
                }

                $count = $records->count();
                $records->each->restore();

                Notification::make()
                    ->title('Users restored')
                    ->body(static::notificationMessage(
                        count: $count,
                        action: 'restored successfully',
                        noun: 'user',
                    ))
                    ->success()
                    ->send();
            });
    }
}
