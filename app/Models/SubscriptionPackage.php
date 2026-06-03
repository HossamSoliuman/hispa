<?php

namespace App\Models;

use App\Casts\UnicodeArrayCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'boats_count',
        'price',
        'original_price',
        'duration_type',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
        'feature_ar',
        'feature_en',
    ];

    protected $casts = [
        'features' => 'array',
        'feature_ar' => UnicodeArrayCast::class,
        'feature_en' => UnicodeArrayCast::class,
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    protected $appends = ['name', 'description'];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? (string) $this->name_ar : (string) $this->name_en;
    }

    /**
     * قائمة المميزات حسب اللغة الحالية (للاستخدام في الواجهات).
     * القيمة في قاعدة البيانات تُخزَّن كـ JSON وتُقرأ كمصفوفة بفضل الـ cast.
     */
    public function getFeaturesAttribute(): array
    {
        $list = app()->getLocale() === 'ar' ? ($this->feature_ar ?? []) : ($this->feature_en ?? []);
        if (is_string($list)) {
            $decoded = json_decode($list, true);
            return is_array($decoded) ? array_values(array_filter($decoded, 'strlen')) : [];
        }
        return is_array($list) ? array_values(array_filter($list, 'strlen')) : [];
    }

    /**
     * وصف مختصر للباقة: أول 3 مميزات مدمجة كنص واحد.
     */
    public function getDescriptionAttribute(): string
    {
        $items = $this->features;
        if (empty($items)) {
            return '';
        }
        $strings = array_map(fn ($item) => is_string($item) ? $item : (is_array($item) ? ($item['text'] ?? $item['name'] ?? '') : ''), $items);
        return implode(' · ', array_slice(array_filter($strings), 0, 3));
    }

    /**
     * السعر الفعلي للمستخدم: إن وُجد سعر عرض (أقل من الأصلي) يُستخدم، وإلا الأصلي.
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
     * هل الباقة لديها سعر عرض (خصم)؟
     */
    public function hasOfferPrice(): bool
    {
        return $this->price !== null
            && (float) $this->price < (float) $this->original_price
            && (float) $this->price >= 0;
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'package_id');
    }
}
