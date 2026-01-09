<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Forms\EditResourceForm;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public Model|int|string|null $record = User::class;

    protected ?string $heading = '';

    public function getBreadcrumbs(): array
    {
        return [
            self::getResource()::getUrl() => 'Users',
            self::getRecord()->name,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return EditResourceForm::make(
            schema: $schema,
            record: self::getRecord(),
            formSchema: UserForm::form(),
            resourceIndexUrl: self::getResource()::getUrl('index'),
        );
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
