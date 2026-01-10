<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Helpers\RoleHelper;
use App\Models\Company;
use Filament\Forms\Components\Hidden;
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

            TextInput::make('name')
                ->label('Name: ')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email address: ')
                ->email()
                ->required()
                ->unique('users', 'email'),

            Select::make('role')
                ->label('Role: ')
                ->native(false)
                ->options(
                    auth()->user()->role === UserRole::SuperAdmin
                        ? RoleHelper::all()
                        : RoleHelper::forCompanyAdmin()
                )
                ->default(UserRole::Staff)
                ->required(),

            Hidden::make('company_id')
                ->default(fn () => auth()->user()->company_id)
                ->visible(fn () => auth()->user()->role !== UserRole::SuperAdmin),

            Select::make('company_id')
                ->label('Company: ')
                ->relationship('company', 'name')
                ->searchable()
                ->options(Company::query()->pluck('name', 'id'))
                ->visible(fn () => auth()->user()->role === UserRole::SuperAdmin)
                ->required(),

            TextInput::make('password')
                ->label('Password: ')
                ->password('create')
                ->required(fn (string $context): bool => $context === 'create')
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state)),

            Toggle::make('is_active')
                ->label('Is active: ')
                ->default(true),
        ];
    }
}
