<?php

namespace App\Models;

use App\Services\CouponService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'usage_limit',
        'times_used',
        'valid_from',
        'valid_until',
        'package_ids',
        'is_active',
        'description_ar',
        'description_en',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'package_ids' => 'array',
        'is_active' => 'boolean',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValidNow($query)
    {
        $now = Carbon::now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
        });
    }

    public function scopeCode($query, string $code)
    {
        return $query->where('code', strtoupper(trim($code)));
    }

    public function isUnlimited(): bool
    {
        return $this->usage_limit === null;
    }

    public function hasRemainingUsage(): bool
    {
        return $this->isUnlimited() || $this->times_used < $this->usage_limit;
    }

    public function isValidForPackage(?int $packageId): bool
    {
        if (empty($this->package_ids)) {
            return true;
        }
        return in_array($packageId, $this->package_ids, true);
    }

    public function isValidAt(Carbon $at = null): bool
    {
        $at = $at ?? Carbon::now();
        if ($this->valid_from && $this->valid_from->gt($at)) {
            return false;
        }
        if ($this->valid_until && $this->valid_until->lt($at)) {
            return false;
        }
        return true;
    }

    /**
     * معاينة الخصم على مبلغ مثال (للعرض في الواجهة).
     *
     * @return array{discount: float, final_amount: float, original: float}
     */
    public function getDiscountPreview(float $sampleAmount): array
    {
        $discount = app(CouponService::class)->calculateDiscount($this, $sampleAmount);
        $final = max(0, $sampleAmount - $discount);
        return [
            'original' => $sampleAmount,
            'discount' => round($discount, 2),
            'final_amount' => round($final, 2),
        ];
    }

    /** عرض قيمة الخصم للمستخدم (نسبة مئوية أو مبلغ). */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return number_format((float) $this->value, 0) . '%';
        }
        return number_format((float) $this->value, 2) . ' ' . (__('admin.units.sar') ?? 'ر.س');
    }
}
