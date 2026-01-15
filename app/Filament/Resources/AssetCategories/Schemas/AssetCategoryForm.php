<?php

namespace App\Filament\Resources\AssetCategories\Schemas;

use App\Models\AssetCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssetCategoryForm
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
                ->required(),

            TextInput::make('code')
                ->label('Code: ')
                ->required(),

            Select::make('parent_id')
                ->label('Parent: ')
                ->searchable()
                ->relationship('parent', 'name')
                ->options(AssetCategory::query()->pluck('name', 'id'))
                ->placeholder('Select parent category'),

        ];
    }
}
