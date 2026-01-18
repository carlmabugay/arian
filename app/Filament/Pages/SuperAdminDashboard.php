<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SuperAdmin\AssetStatusWidget;
use App\Filament\Widgets\SuperAdmin\AssignmentActivityWidget;
use App\Filament\Widgets\SuperAdmin\AuditLogWidget;
use App\Filament\Widgets\SuperAdmin\CompanyHealthWidget;
use App\Filament\Widgets\SuperAdmin\SystemStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class SuperAdminDashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.super-admin-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

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
        ];
    }
}
