<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Enums\SystemAction;
use App\Filament\Forms\EditResourceForm;
use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Companies\Schemas\CompanyForm;
use App\Helpers\SystemMessageHelper;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected ?string $heading = '';

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl() => 'Companies',
            self::getRecord()->name,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return EditResourceForm::make(
            schema: $schema,
            record: self::getRecord(),
            formSchema: CompanyForm::form(),
            resourceIndexUrl: url()->previous(),
        );
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return SystemMessageHelper::successTitle(
            SystemAction::Update,
            self::getRecord()->name
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
