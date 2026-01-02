<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

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
