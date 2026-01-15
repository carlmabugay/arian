<?php

namespace App\Filament\Resources\AssetCategories\Pages;

use App\Enums\SystemAction;
use App\Filament\Forms\EditResourceForm;
use App\Filament\Resources\AssetCategories\AssetCategoryResource;
use App\Filament\Resources\AssetCategories\Schemas\AssetCategoryForm;
use App\Helpers\SystemMessageHelper;
use App\Models\AssetCategory;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
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
        return EditResourceForm::make(
            schema: $schema,
            record: self::getRecord(),
            formSchema: AssetCategoryForm::form(),
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
