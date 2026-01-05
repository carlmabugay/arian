<?php

namespace App\Filament\Resources\AssetAssignments\Schemas;

use App\Models\Asset;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AssetAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->relationship('asset', 'name')
                    ->label('Asset: ')
                    ->searchable()
                    ->options(Asset::query()->pluck('name', 'id'))
                    ->required()
                    ->columnSpanFull(),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Assigned To: ')
                    ->searchable()
                    ->options(User::query()->pluck('name', 'id'))
                    ->required()
                    ->columnSpanFull(),

                Select::make('assigned_by')
                    ->relationship('assignedBy', 'name')
                    ->label('Assigned By: ')
                    ->searchable()
                    ->options(User::query()->pluck('name', 'id'))
                    ->required()
                    ->columnSpanFull(),

                DateTimePicker::make('assigned_at')
                    ->label('Assigned On: ')
                    ->native(false)
                    ->required()
                    ->columnSpanFull(),

                DateTimePicker::make('returned_at')
                    ->label('Return On: ')
                    ->native(false)
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
