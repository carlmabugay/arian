<?php

namespace App\Filament\Widgets\SuperAdmin;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetStatusWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Asset Status Breakdown';

    protected string $color = 'primary';

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Assets',
                    'data' => collect(AssetStatus::cases())
                        ->map(fn ($status) => Asset::where('status', $status)->count()
                        )
                        ->toArray(),
                ],
            ],
            'labels' => collect(AssetStatus::cases())
                ->map(fn ($status) => ucfirst($status->value))
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
