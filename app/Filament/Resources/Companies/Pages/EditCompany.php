<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Company;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected ?string $heading = '';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                Section::make(fn (?Company $record) => "Edit {$record->name}")
                    ->headerActions([
                        DeleteAction::make()
                            ->label('')
                            ->icon('heroicon-o-trash')
                            ->size(Size::ExtraSmall),
                        ForceDeleteAction::make(),
                        RestoreAction::make(),
                    ])
                    ->schema([

                        TextInput::make('name')
                            ->label('Name: ')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('code')
                            ->label('Code: ')
                            ->required()
                            ->columnSpanFull(),
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

            );
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
