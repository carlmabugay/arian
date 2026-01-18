<?php

namespace App\Filament\Widgets\CompanyAdmin;

use App\Models\AssetAssignment;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class AssignmentActivityWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return ! auth()->user()->isStaff();
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        return AssetAssignment::query()
            ->whereHas('asset', fn ($query) => $query->where('company_id', auth()->user()->company_id))
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('asset.name'),
            Tables\Columns\TextColumn::make('user.name'),
            Tables\Columns\TextColumn::make('created_at')->since(),
        ];
    }
}
