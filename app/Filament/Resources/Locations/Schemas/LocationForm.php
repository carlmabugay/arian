<?php

namespace App\Filament\Resources\Locations\Schemas;

use App\Enums\LocationType;
use App\Enums\UserRole;
use App\Models\Company;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('company_id')
                    ->label('Company: ')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->options(Company::query()->pluck('name', 'id'))
                    ->default(fn () => auth()->user()->company_id)
                    ->visible(fn () => auth()->user()->role !== UserRole::SuperAdmin)
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Name: ')
                    ->required()
                    ->columnSpanFull(),

                Select::make('type')
                    ->label('Type: ')
                    ->options(LocationType::class)
                    ->default(LocationType::Office)
                    ->native(false)
                    ->required()
                    ->columnSpanFull(),

            ]);
    }
}
