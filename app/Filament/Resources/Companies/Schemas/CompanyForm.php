<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::form());
    }

    public static function form(): array
    {
        return [
            TextInput::make('name')
                ->label('Names: ')
                ->required()
                ->maxLength(255),

            TextInput::make('code')
                ->label('Code: ')
                ->required('create')
                ->unique('companies', 'code')
                ->readonly('edit'),
        ];
    }
}
