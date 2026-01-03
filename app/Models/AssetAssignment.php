<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'returned_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    protected $attributes = [
        'returned_at' => null,
    ];

    public function asset(): belongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): belongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
