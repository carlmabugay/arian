<?php

namespace App\Filament\Resources\AssetCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('parent_id')
                    ->label('Parent Category: ')
                    ->searchable()
                    ->relationship('parent', 'name')
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Name: ')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('code')
                    ->label('Code: ')
                    ->required()
                    ->columnSpanFull(),

            ]);
    }
}
