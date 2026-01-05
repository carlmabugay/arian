<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use App\Filament\Resources\AssetAssignments\AssetAssignmentResource;
use App\Filament\Resources\Assets\AssetResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Str;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $relatedResource = AssetAssignmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Assignment History')
            ->description(sprintf('Manage %s assignments here.', Str::lower($this->ownerRecord->name)))
            ->headerActions([
                Action::make('create')
                    ->icon(Heroicon::OutlinedPlus)
                    ->label('Add new')
                    ->size(Size::Small)
                    ->slideOver()
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Assigned To: ')
                            ->searchable()
                            ->options(User::query()->pluck('name', 'id'))
                            ->required(),

                        Select::make('assigned_by')
                            ->relationship('assignedBy', 'name')
                            ->label('Assigned By: ')
                            ->searchable()
                            ->options(User::query()->pluck('name', 'id'))
                            ->required(),

                        DateTimePicker::make('assigned_at')
                            ->label('Assigned On: ')
                            ->native(false)
                            ->required(),

                        DateTimePicker::make('returned_at')
                            ->label('Return On: ')
                            ->native(false),

                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->modalHeading(sprintf('Add new %s assignment', $this->ownerRecord->name))
                    ->modalWidth(Width::Medium)
                    ->modalFooterActions([
                        Action::make('create')->submit('create'),
                        Action::make('cancel')
                            ->color('gray')
                            ->url(fn () => AssetResource::getUrl('edit', [
                                'record' => $this->getOwnerRecord(),
                            ])),
                    ])
                    ->action(fn (array $data) => $this->ownerRecord->assignments()->create($data)),
            ]);
    }
}
