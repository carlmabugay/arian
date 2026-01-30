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

    protected static ?string $heading = 'Recent Assignments';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return ! auth()->user()->isStaff();
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        $user = auth()->user();

        $query = AssetAssignment::query()->latest();

        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereHas(
            'asset',
            fn (Builder $q) => $q->where('company_id', $user->company_id)
        );
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('asset.name'),
            Tables\Columns\TextColumn::make('user.name'),
            Tables\Columns\TextColumn::make('created_at')->label('Created')->since(),
        ];
    }
}
