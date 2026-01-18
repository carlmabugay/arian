<?php

namespace App\Filament\Widgets\SuperAdmin;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Company;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Companies', Company::count()),
            Stat::make('Users', User::count()),
            Stat::make('Assets', Asset::count()),
            Stat::make(
                'Active Assignments',
                AssetAssignment::whereNull('returned_at')->count()
            ),
        ];
    }
}
