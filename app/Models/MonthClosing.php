<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthClosing extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'boat_id' => 'integer',
            'gross_sales' => 'decimal:2',
            'net_sales' => 'decimal:2',
            'net_owner_revenue' => 'decimal:2',
            'trip_expenses' => 'decimal:2',
            'general_expenses' => 'decimal:2',
            'fixed_salaries' => 'decimal:2',
            'depreciation' => 'decimal:2',
            'asset_depreciation_breakdown' => 'array',
            'total_expenses' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'owner_percent' => 'decimal:2',
            'owner_share' => 'decimal:2',
            'crew_share' => 'decimal:2',
            'share_value' => 'decimal:2',
            'total_shares' => 'decimal:2',
            'closed_at' => 'datetime',
        ];
    }

    public function dues(): HasMany
    {
        return $this->hasMany(MonthClosingDue::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class, 'boat_id');
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
