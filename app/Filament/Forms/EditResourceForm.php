<?php

namespace App\Filament\Forms;

use App\Enums\SystemAction;
use App\Filament\Traits\ConfigureSystemAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditResourceForm
{
    use ConfigureSystemAction;

    public static function make(Schema $schema, Model $record, array $formSchema, string $resourceIndexUrl, string $titleAttribute = 'name'): Schema
    {

        return $schema
            ->components(
                Section::make(sprintf('Edit %s', $record->{$titleAttribute}))
                    ->headerActions([
                        DeleteAction::make()
                            ->label('')
                            ->icon(Heroicon::OutlinedTrash)
                            ->size(Size::ExtraSmall)
                            ->color('warning')
                            ->tooltip('Trash')
                            ->tap(fn (Action $action) => static::configureAction($action, SystemAction::Delete, $record->name))
                            ->authorize('delete', $record),

                        RestoreAction::make()
                            ->label('')
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->size(Size::ExtraSmall)
                            ->color('success')
                            ->tooltip('Restore')
                            ->tap(fn (Action $action) => static::configureAction($action, SystemAction::Restore, $record->name))
                            ->authorize('restore', $record),

                        ForceDeleteAction::make()
                            ->label('')
                            ->icon(Heroicon::OutlinedXMark)
                            ->size(Size::ExtraSmall)
                            ->color('danger')
                            ->tooltip('Delete Permanently')
                            ->tap(fn (Action $action) => static::configureAction($action, SystemAction::ForceDelete, $record->name))
                            ->authorize('forceDelete', $record),

                    ])
                    ->schema($formSchema)
                    ->footerActions([
                        Action::make('save')
                            ->submit('save')
                            ->size(Size::Small),

                        Action::make('cancel')
                            ->label('Cancel')
                            ->color('gray')
                            ->outlined()
                            ->url($resourceIndexUrl)
                            ->size(Size::Small),
                    ])
                    ->footerActionsAlignment(Alignment::End)
                    ->columns()
                    ->columnSpanFull()

            );
    }
}
