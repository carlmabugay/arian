<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Enums\SystemAction;
use App\Filament\Forms\EditResourceForm;
use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Assets\Schemas\AssetForm;
use App\Helpers\SystemMessageHelper;
use App\Models\Asset;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    public Model|int|string|null $record = Asset::class;

    protected ?string $heading = '';

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => 'Assets',
            null => $this->record->name,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return EditResourceForm::make(
            schema: $schema,
            record: self::getRecord(),
            formSchema: AssetForm::form(),
            resourceIndexUrl: self::getResource()::getUrl('index'),
        );
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return SystemMessageHelper::successTitle(
            SystemAction::Update,
            $this->record->name,
        );
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
