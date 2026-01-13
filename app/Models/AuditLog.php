<?php

namespace App\Models;

use App\Enums\SystemAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'action',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'action' => SystemAction::class,
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getChanges(): array
    {
        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];

        return collect($new)
            ->filter(fn ($value, $key) => ($old[$key] ?? null) != $value)
            ->mapWithKeys(fn ($value, $key) => [
                $key => [
                    'old' => $old[$key] ?? null,
                    'new' => $value,
                ],
            ])
            ->toArray();
    }
}
