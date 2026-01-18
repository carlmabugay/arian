<?php

namespace App\Filament\Widgets\CompanyAdmin;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()->isCompanyAdmin();
    }

    protected function getStats(): array
    {
        $company = auth()->user()->company;

        return [
            Stat::make('Assets', $company->assets()->count()),
            Stat::make('Assigned', $company->assets()->whereNotNull('user_id')->count()),
            Stat::make('Users', $company->users()->count()),
        ];
    }
}
