<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CompanyAdmin\AssetStatsWidget;
use App\Filament\Widgets\CompanyAdmin\AssignmentActivityWidget as CompanyAssignmentActivityWidget;
use App\Filament\Widgets\CompanyAdmin\CompanyOverviewWidget;
use App\Filament\Widgets\SuperAdmin\AssetStatusWidget;
use App\Filament\Widgets\SuperAdmin\AssignmentActivityWidget;
use App\Filament\Widgets\SuperAdmin\AuditLogWidget;
use App\Filament\Widgets\SuperAdmin\CompanyHealthWidget;
use App\Filament\Widgets\SuperAdmin\SystemStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';

    public function getHeading(): string|Htmlable|null
    {
        $name = auth()->user()->name;

        return "Welcome back, {$name}!";
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SystemStatsWidget::class,
            CompanyHealthWidget::class,
            AssetStatusWidget::class,
            AssignmentActivityWidget::class,
            AuditLogWidget::class,

            CompanyOverviewWidget::class,
            AssetStatsWidget::class,
            CompanyAssignmentActivityWidget::class,
        ];
    }
}
