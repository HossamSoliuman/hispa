<?php

namespace App\Models;

use App\Observers\SaleDetailObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

// #[ObservedBy(SaleDetailObserver::class)]

class SaleDetail extends Model
{
    protected $fillable = [
        'sale_id',
        'fish_id',
        'unit_id',
        'fish_name',
        'quantity',
        'weight',
        'price_per_kilo',
        'total_price',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class)->withDefault();
    }

    public function dalalStockDetail()
    {
        return $this->belongsTo(DalalStockDetail::class);
    }

    public function fish()
    {
        return $this->belongsTo(Fish::class)->withDefault();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class)->withDefault();
    }
}
