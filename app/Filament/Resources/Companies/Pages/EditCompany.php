<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Company;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    public Model|int|string|null $record = Company::class;

    protected ?string $heading = '';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;

        return auth()->user()->can('update', $record);
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => 'Companies',
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
                            ->size(Size::ExtraSmall)
                            ->authorize('delete', $this->record),
                        ForceDeleteAction::make()
                            ->size(Size::ExtraSmall)
                            ->authorize('forceDelete', $this->record)
                            ->successNotificationTitle($this->record->name.' is now permanently deleted'),
                        RestoreAction::make()
                            ->size(Size::ExtraSmall)
                            ->authorize('restore', $this->record)
                            ->successNotificationTitle($this->record->name.' is now restored'),
                    ])
                    ->schema([

                        TextInput::make('name')
                            ->label('Name: ')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label('Code: ')
                            ->readonly(),
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
