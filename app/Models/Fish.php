<?php

namespace App\Models;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fish extends Model
{
    use BelongsToOwner, SoftDeletes;

    protected $table = 'fish';

    protected $fillable = [
        'code',
        'scientific_name',
        'english_name',
        'local_name_primary',
        'local_name_secondary',
        'status',
        'red_sea_name',
        'arabian_gulf_name',
        'region_id',
        'governorate_id',
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

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();

        if ($locale === 'en') {
            return $this->attributes['scientific_name'] ?? $this->attributes['english_name'] ?? '--';
        }

        // استخدام data_get لتجنب Undefined array key
        $redSea = data_get($this->attributes, 'red_sea_name');
        $arabianGulf = data_get($this->attributes, 'arabian_gulf_name');

        return $redSea ?: $arabianGulf ?: $this->attributes['scientific_name'] ?? '--';
    }
}
