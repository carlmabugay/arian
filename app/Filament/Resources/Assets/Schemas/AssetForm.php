<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Filament\Resources\Assets\AssetResource;
use App\Models\AssetCategory;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(
                Section::make([
                    Select::make('company_id')
                        ->relationship('company', 'name')
                        ->label('Company: ')
                        ->searchable()
                        ->options(Company::query()->pluck('name', 'id'))
                        ->required(),

                    Select::make('asset_category_id')
                        ->relationship('category', 'name')
                        ->label('Category: ')
                        ->searchable()
                        ->options(AssetCategory::query()->pluck('name', 'id'))
                        ->required(),

                    Select::make('status')
                        ->label('Status: ')
                        ->native(false)
                        ->options(AssetStatus::class)
                        ->default(AssetStatus::Available)
                        ->required(),

                    Select::make('condition')
                        ->label('Condition: ')
                        ->native(false)
                        ->options(AssetCondition::class)
                        ->default(AssetCondition::New)
                        ->required(),

                    TextInput::make('asset_tag')
                        ->label('Asset Tag: ')
                        ->required(),

                    TextInput::make('serial_number')
                        ->label('Serial Number: '),

                    TextInput::make('name')
                        ->label('Name: ')
                        ->required(),

                    Textarea::make('description')
                        ->label('Description: ')
                        ->columnSpanFull(),

                    DatePicker::make('purchased_at')
                        ->label('Purchased Date: ')
                        ->native(false),

                    TextInput::make('purchase_price')
                        ->label('Purchase Price: ')
                        ->numeric()
                        ->prefix('$'),

                    Select::make('location_id')
                        ->label('Location: ')
                        ->relationship('location', 'name')
                        ->searchable()
                        ->options(Location::query()->pluck('name', 'id'))
                        ->required(),

                    Select::make('user_id')
                        ->label('Assigned To: ')
                        ->relationship('assignedUser', 'name')
                        ->searchable()
                        ->options(User::query()->pluck('name', 'id')),
                ])
                    ->footerActions([
                        Action::make('save')
                            ->submit('save')
                            ->size(Size::Small),

                        Action::make('cancel')
                            ->label('Cancel')
                            ->color('gray')
                            ->outlined()
                            ->url(AssetResource::getUrl('index'))
                            ->size(Size::Small),
                    ])
                    ->footerActionsAlignment(Alignment::End)
                    ->columns()
                    ->columnSpanFull()
            );

    }
}
