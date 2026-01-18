<?php

namespace App\Filament\Widgets\SuperAdmin;

use App\Models\Company;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyHealthWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    protected function getStats(): array
    {
        $companiesWithoutAdmin = Company::whereDoesntHave('users', fn ($query) => $query->companyAdmin())->count();

        return [
            Stat::make('Companies w/o Admin', $companiesWithoutAdmin)
                ->description('Governance risk')
                ->color('danger'),

            Stat::make(
                'Trashed Companies',
                Company::onlyTrashed()->count()
            ),
        ];
    }
}
