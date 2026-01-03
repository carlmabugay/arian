<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Company;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public Model|int|string|null $record = User::class;

    protected ?string $heading = '';

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => 'Users',
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

                        Select::make('company_id')
                            ->label('Company: ')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->options(Company::query()->pluck('name', 'id'))
                            ->required(),

                        TextInput::make('name')
                            ->label('Name: ')
                            ->required(),

                        TextInput::make('email')
                            ->label('Email address: ')
                            ->email()
                            ->required()
                            ->unique('users', 'email'),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email verified at: ')
                            ->native(false),

                        TextInput::make('password')
                            ->label('Password: ')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('Is active: ')
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
                    ->columns()
                    ->columnSpanFull()
            );
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
