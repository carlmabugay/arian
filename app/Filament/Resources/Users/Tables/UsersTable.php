<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\Schemas\UserForm;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Users')
            ->description('Manage users here.')
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus)
                    ->label('Add new')
                    ->size(Size::Small)
                    ->slideOver()
                    ->modalHeading('User Details')
                    ->modalWidth(Width::Medium)
                    ->schema(UserForm::form())
                    ->authorize('create'),
            ])
            ->columns([
                TextColumn::make('company.name')
                    ->label('Company'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('role')
                    ->badge(),
                TextColumn::make('email_verified_at')
                    ->label('Email Verification Date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creation Date')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions(
                ActionGroup::make([
                    EditAction::make()
                        ->slideOver()
                        ->modalHeading('Edit User')
                        ->modalWidth('md'),
                ])
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
