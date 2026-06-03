<?php

namespace App\Models;

use App\Observers\FishStockObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(FishStockObserver::class)]
class FishStock extends Model
{
    use SoftDeletes;

    protected $table = 'fish_stocks';

    protected $fillable = [
        'trip_id',
        'fish_id',
        'fish_name',
        'weight',
        'quantity',
        'quantity_captain',
        'weight_captain',
        'quantity_counter',
        'weight_counter',
        'added_by',
        'corrected_by',
        'notes',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'weight_captain' => 'decimal:2',
        'weight_counter' => 'decimal:2',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function fish(): BelongsTo
    {
        return $this->belongsTo(Fish::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}
