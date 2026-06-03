<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FishStockHistory extends Model
{
    protected $table = 'fish_stock_histories';

    protected $fillable = [
        'fish_stock_id',
        'fish_id',
        'operation_type',
        'changed_weight',
        'before_weight',
        'after_weight',
        'notes',
        'done_by',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(FishStock::class, 'fish_stock_id');
    }

    public function fish(): BelongsTo
    {
        return $this->belongsTo(Fish::class, 'fish_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'done_by');
    }
}
