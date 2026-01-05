<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
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
            ->heading(sprintf('%s Users', $this->ownerRecord->name))
            ->description(sprintf('Manage %s users here.', $this->ownerRecord->name))
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus)
                    ->label('Add new')
                    ->size(Size::Small)
                    ->slideOver()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name: ')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label('Email address: ')
                            ->email()
                            ->required()
                            ->unique('users', 'email')
                            ->columnSpanFull(),

                        TextInput::make('password')
                            ->label('Password: ')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Is active: ')
                            ->required(),
                    ])
                    ->modalHeading(sprintf('Add new %s user.', $this->ownerRecord->name))
                    ->modalWidth(Width::Medium),
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
