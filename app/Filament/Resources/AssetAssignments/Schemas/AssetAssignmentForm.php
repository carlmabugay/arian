<?php

namespace App\Filament\Resources\AssetAssignments\Schemas;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AssetAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->relationship(
                        'asset',
                        'name',
                    )
                    ->label('Asset: ')
                    ->searchable()
                    ->options(
                        auth()->user()->isSuperAdmin() ?
                            Asset::query()->where('status', AssetStatus::Available)->pluck('name', 'id') :
                        Asset::query()
                            ->where('company_id', auth()->user()->company_id)
                            ->where('status', AssetStatus::Available)
                            ->pluck('name', 'id')
                    )
                    ->required()
                    ->columnSpanFull(),

                Select::make('user_id')
                    ->relationship(
                        'user',
                        'name',
                        fn (Builder $query) => auth()->user()->isSuperAdmin()
                            ? $query
                            : $query->where('company_id', auth()->user()->company_id)
                    )
                    ->label('Assigned To: ')
                    ->searchable()
                    ->options(
                        auth()->user()->isSuperAdmin() ?
                            User::query()->pluck('name', 'id') :
                            User::query()->where('company_id', auth()->user()->company_id)->pluck('name', 'id')
                    )
                    ->required()
                    ->columnSpanFull(),

                DateTimePicker::make('assigned_at')
                    ->label('Assigned On: ')
                    ->native(false)
                    ->default(now())
                    ->required()
                    ->columnSpanFull(),

                DateTimePicker::make('returned_at')
                    ->label('Return On: ')
                    ->native(false)
                    ->visibleOn('edit')
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
