<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Summary')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('action')
                            ->label('Action: ')
                            ->badge(),

                        TextEntry::make('user.name')
                            ->label('Actor: ')
                            ->placeholder('System'),

                        TextEntry::make('auditable_type')
                            ->label('Model: ')
                            ->formatStateUsing(fn ($state) => class_basename($state)),

                        TextEntry::make('created_at')
                            ->label('Date: ')
                            ->dateTime(),

                        TextEntry::make('ip_address')
                            ->label('IP Address: '),

                        TextEntry::make('user_agent')
                            ->label('User Agent: '),

                    ])->columnSpanFull(),

                Section::make('Log')
                    ->schema([
                        RepeatableEntry::make('changes')
                            ->label(null)
                            ->state(fn ($record) => collect($record->getChanges())->map(
                                fn ($change, $field) => [
                                    'field' => Str::headline($field),
                                    'old' => $change['old'] ?? '—',
                                    'new' => $change['new'] ?? '—',
                                ]
                            )->values()->toArray())
                            ->schema([
                                TextEntry::make('field')->label('Field: '),
                                TextEntry::make('old')->label('Old: ')->color('gray'),
                                TextEntry::make('new')->label('New: ')->color('success'),
                            ])
                            ->placeholder('Nothing found')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}
