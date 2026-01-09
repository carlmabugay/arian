<?php

namespace App\Filament\Forms;

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
    public static function make(Schema $schema, Model $record, array $formSchema, string $resourceIndexUrl, string $titleAttribute = 'name'): Schema
    {

        return $schema
            ->components(
                Section::make(sprintf('Edit %s details', $record->{$titleAttribute}))
                    ->headerActions([
                        DeleteAction::make()
                            ->label('')
                            ->icon(Heroicon::OutlinedTrash)
                            ->size(Size::ExtraSmall)
                            ->color('warning')
                            ->tooltip('Trash')
                            ->authorize('delete', $record),

                        RestoreAction::make()
                            ->label('')
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->size(Size::ExtraSmall)
                            ->color('success')
                            ->authorize('restore', $record)
                            ->tooltip('Restore')
                            ->successNotificationTitle($record->{$titleAttribute}.' is now restored'),

                        ForceDeleteAction::make()
                            ->label('')
                            ->icon(Heroicon::OutlinedXMark)
                            ->size(Size::ExtraSmall)
                            ->authorize('forceDelete', $record)
                            ->tooltip('Permanently delete')
                            ->successNotificationTitle($record->{$titleAttribute}.' is now permanently deleted'),

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
