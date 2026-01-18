<?php

namespace App\Filament\Widgets\CompanyAdmin;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->isCompanyAdmin();
    }

    protected function getStats(): array
    {
        $company = auth()->user()->company;

        return [
            Stat::make('Available', $company->assets()->whereNull('user_id')->count()),
            Stat::make('In Use', $company->assets()->whereNotNull('user_id')->count()),
            Stat::make('In Repair', $company->assets()->where('status', 'repair')->count()),
        ];
    }
}
