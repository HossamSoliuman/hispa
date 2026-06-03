<?php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CouponService
{
    /**
     * Validate coupon by code for a given package and return coupon + discount info or error message.
     *
     * @return array{valid: bool, coupon?: Coupon, discount_amount?: float, message?: string}
     */
    public function validate(string $code, float $amount, ?int $packageId = null): array
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return ['valid' => false, 'message' => __('admin.coupons.invalid_code')];
        }

        $coupon = Coupon::active()
            ->code($code)
            ->validNow()
            ->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => __('admin.coupons.not_found_or_expired')];
        }

        if (!$coupon->hasRemainingUsage()) {
            return ['valid' => false, 'message' => __('admin.coupons.usage_limit_reached')];
        }

        if (!$coupon->isValidForPackage($packageId)) {
            return ['valid' => false, 'message' => __('admin.coupons.not_applicable_to_package')];
        }

        $discountAmount = $this->calculateDiscount($coupon, $amount);
        if ($discountAmount <= 0) {
            return ['valid' => false, 'message' => __('admin.coupons.no_discount')];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount_amount' => round($discountAmount, 2),
            'final_amount' => round(max(0, $amount - $discountAmount), 2),
        ];
    }

    /**
     * Calculate discount amount for a coupon and given amount.
     */
    public function calculateDiscount(Coupon $coupon, float $amount): float
    {
        if ($coupon->type === Coupon::TYPE_PERCENTAGE) {
            $percent = min(100, max(0, (float) $coupon->value));
            return round($amount * ($percent / 100), 2);
        }

        $fixed = max(0, (float) $coupon->value);
        return min($fixed, $amount);
    }

    /**
     * Apply coupon: increment times_used and return discount amount. Call within transaction.
     */
    public function applyUsage(Coupon $coupon): void
    {
        $coupon->increment('times_used');
    }
}
