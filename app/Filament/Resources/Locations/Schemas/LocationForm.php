<?php

namespace App\Filament\Resources\Locations\Schemas;

use App\Enums\LocationType;
use App\Enums\UserRole;
use App\Models\Company;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::form());
    }

    public static function form(): array
    {
        return [
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

            TextInput::make('name')
                ->label('Name: ')
                ->required(),

            Select::make('type')
                ->label('Type: ')
                ->options(LocationType::class)
                ->default(LocationType::Office)
                ->native(false)
                ->required(),
        ];
    }
}
