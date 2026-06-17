<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthClosingDue extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'shares' => 'decimal:2',
            'custom_share_percent' => 'decimal:2',
            'share_value' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'advances' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining' => 'decimal:2',
            'is_paid' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    public function monthClosing(): BelongsTo
    {
        return $this->belongsTo(MonthClosing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
