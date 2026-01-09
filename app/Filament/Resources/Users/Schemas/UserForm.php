<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Company;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::form());
    }

    public static function form(): array
    {
        return [

            Select::make('company_id')
                ->label('Company: ')
                ->relationship('company', 'name')
                ->searchable()
                ->options(Company::query()->pluck('name', 'id'))
                ->required()
                ->columnSpanFull(),

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

            DateTimePicker::make('email_verified_at')
                ->label('Email verified at: ')
                ->native(false)
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
        ];
    }
}
