<?php

namespace App\Filament\Widgets\SuperAdmin;

use App\Models\AuditLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AuditLogWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Created (24h)',
                AuditLog::where('event', 'created')
                    ->where('created_at', '>=', now()->subDay())
                    ->count()
            ),
            Stat::make(
                'Updated (24h)',
                AuditLog::where('event', 'updated')
                    ->where('created_at', '>=', now()->subDay())
                    ->count()
            ),
            Stat::make(
                'Deleted (24h)',
                AuditLog::whereIn('event', ['deleted', 'force_deleted'])
                    ->where('created_at', '>=', now()->subDay())
                    ->count()
            ),
        ];
    }
}
