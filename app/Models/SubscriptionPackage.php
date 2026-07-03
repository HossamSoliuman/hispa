<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPackage extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'boats_count',
        'price',
        'original_price',
        'duration_type',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'boats_count' => 'integer',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    protected $appends = ['name', 'description'];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? (string) $this->name_ar : (string) $this->name_en;
    }

    /**
     * Short localized description of the plan: how many boats it allows.
     */
    public function getDescriptionAttribute(): string
    {
        return $this->boatsLabel();
    }

    /**
     * Localized "N boats" label (with Arabic dual/plural handling).
     */
    public function boatsLabel(): string
    {
        $count = (int) $this->boats_count;

        if (app()->getLocale() === 'ar') {
            return match (true) {
                $count === 1 => 'قارب واحد',
                $count === 2 => 'قاربان',
                $count >= 3 && $count <= 10 => $count.' قوارب',
                default => $count.' قارب',
            };
        }

        return $count === 1 ? '1 boat' : $count.' boats';
    }

    /**
     * The price the customer actually pays: the offer price when it is a real
     * discount, otherwise the original price.
     */
    public function getEffectivePriceAttribute(): float
    {
        $original = (float) $this->original_price;
        $offer = $this->price !== null ? (float) $this->price : null;

        if ($offer !== null && $offer < $original) {
            return $offer;
        }

        return $original;
    }

    /**
     * Whether this plan has a real discount (offer price below the original).
     */
    public function hasOfferPrice(): bool
    {
        return $this->price !== null
            && (float) $this->price < (float) $this->original_price
            && (float) $this->price >= 0;
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'package_id');
    }
}
