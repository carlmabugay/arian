<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Filament\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'asset_category_id',
        'status',
        'condition',
        'asset_tag',
        'serial_number',
        'name',
        'description',
        'purchased_at',
        'purchase_price',
        'location_id',
        'user_id',
    ];

    protected $casts = [
        'status' => AssetStatus::class,
        'condition' => AssetCondition::class,
        'purchased_at' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => AssetStatus::Available,
        'condition' => AssetCondition::New,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query
            ->whereNull('deleted_at')
            ->whereNotIn('status', [
                AssetStatus::Retired,
            ]);
    }

    public function isAssigned(): bool
    {
        return $this->status === AssetStatus::Assigned;
    }
}
