<?php

namespace App\Filament\Resources\AssetCategories\RelationManagers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Str;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected static ?string $relatedResource = AssetResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->heading(sprintf('%s assets.', Str::ucwords($this->ownerRecord->name)))
            ->description(sprintf('Manage %s assets here.', Str::lower($this->ownerRecord->name)))
            ->headerActions([
                Action::make('create')
                    ->icon(Heroicon::OutlinedPlus)
                    ->label('Add new')
                    ->size(Size::Small)
                    ->slideOver()
                    ->schema([

                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->label('Company: ')
                            ->searchable()
                            ->options(Company::query()->pluck('name', 'id'))
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
                    ->modalHeading(sprintf('Add new %s asset.', $this->ownerRecord->name))
                    ->modalWidth(Width::Medium)
                    ->modalFooterActions([
                        Action::make('create')->submit('create'),
                        Action::make('cancel')
                            ->color('gray')
                            ->url(fn () => CompanyResource::getUrl('edit', [
                                'record' => $this->getOwnerRecord(),
                            ])),
                    ])
                    ->action(fn (array $data) => $this->ownerRecord->assets()->create($data))
                    ->successNotificationTitle('Created'),
            ])
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->searchable(),
                TextColumn::make('assignedUser.name')
                    ->label('Assigned to'),
                TextColumn::make('condition')
                    ->badge(),

            ])
            ->defaultSort('created_at', 'desc');
    }
}
