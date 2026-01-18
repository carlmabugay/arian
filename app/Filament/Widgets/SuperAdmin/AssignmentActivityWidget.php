<?php

namespace App\Filament\Widgets\SuperAdmin;

use App\Models\AssetAssignment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AssignmentActivityWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Assignment Activity (Last 7 Days)';

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Assignments',
                    'data' => collect(range(6, 0))
                        ->map(fn ($days) => AssetAssignment::whereDate(
                            'created_at',
                            Carbon::now()->subDays($days)
                        )->count()
                        )->toArray(),
                ],
            ],
            'labels' => collect(range(6, 0))
                ->map(fn ($days) => Carbon::now()->subDays($days)->format('M d')
                )->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
