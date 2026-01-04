<?php

namespace App\Filament\Resources\AssetCategories\Pages;

use App\Filament\Resources\AssetCategories\AssetCategoryResource;
use App\Models\AssetCategory;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditAssetCategory extends EditRecord
{
    protected static string $resource = AssetCategoryResource::class;

    public Model|int|string|null $record = AssetCategory::class;

    protected ?string $heading = '';

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => 'Categories',
            self::$resource::getUrl('edit', ['record' => $this->record->id]) => $this->record->name,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                Section::make(sprintf('Edit %s', $this->record->name))
                    ->headerActions([
                        DeleteAction::make()
                            ->label('')
                            ->icon(Heroicon::OutlinedTrash)
                            ->size(Size::ExtraSmall),
                        ForceDeleteAction::make(),
                        RestoreAction::make(),
                    ])
                    ->schema([

                        Select::make('parent_id')
                            ->label('Parent Category: ')
                            ->searchable()
                            ->relationship('parent', 'name')
                            ->options(AssetCategory::query()->pluck('name', 'id')),

                        TextInput::make('name')
                            ->label('Name: ')
                            ->required(),

                        TextInput::make('code')
                            ->label('Code: ')
                            ->required(),

                    ])
                    ->footerActions([
                        Action::make('save')
                            ->submit('save')
                            ->size(Size::Small),

                        Action::make('cancel')
                            ->label('Cancel')
                            ->color('gray')
                            ->outlined()
                            ->url($this->getResource()::getUrl('index'))
                            ->size(Size::Small),
                    ])
                    ->footerActionsAlignment(Alignment::End)
                    ->columns()
                    ->columnSpanFull()

            );
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
