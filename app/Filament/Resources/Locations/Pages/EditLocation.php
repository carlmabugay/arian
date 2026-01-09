<?php

namespace App\Filament\Resources\Locations\Pages;

use App\Filament\Forms\EditResourceForm;
use App\Filament\Resources\Locations\LocationResource;
use App\Filament\Resources\Locations\Schemas\LocationForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditLocation extends EditRecord
{
    protected static string $resource = LocationResource::class;

    protected ?string $heading = '';

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl() => 'Locations',
            self::getRecord()->name,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return EditResourceForm::make(
            schema: $schema,
            record: self::getRecord(),
            formSchema: LocationForm::form(),
            resourceIndexUrl: self::getResource()::getUrl('index'),
        );
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
