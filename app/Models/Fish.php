<?php

namespace App\Models;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fish extends Model
{
    use BelongsToOwner;

    protected $table = 'fish';

    protected $fillable = [
        'name_ar',
        'name_en',
        'status',
        'owner_id',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    protected $appends = ['name'];

    // scopes for active fish
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function fishQuantityStocks(): HasMany
    {
        return $this->hasMany(FishQuantityStock::class);
    }

    public function catchDetails(): HasMany
    {
        return $this->hasMany(CatchDetail::class, 'fish_id');
    }

    public function getNameAttribute(): string
    {
        $nameAr = data_get($this->attributes, 'name_ar');
        $nameEn = data_get($this->attributes, 'name_en');

        if (app()->getLocale() === 'en') {
            return $nameEn ?: ($nameAr ?: '--');
        }

        return $nameAr ?: ($nameEn ?: '--');
    }
}
