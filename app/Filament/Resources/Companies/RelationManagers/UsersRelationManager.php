<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Company Users')
            ->description(sprintf('Manage %s users here.', $this->ownerRecord->name))
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus)
                    ->label('Add new')
                    ->size(Size::Small)
                    ->slideOver()
                    ->modalHeading('Add New User')
                    ->modalWidth('md'),
            ])
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->searchable(),

                    TextColumn::make('email')
                        ->label('Email address')
                        ->searchable(),
                ]),
            ])
            ->filtersLayout(FiltersLayout::AfterContentCollapsible);
    }
}
