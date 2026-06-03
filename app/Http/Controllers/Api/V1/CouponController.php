<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponService $couponService
    ) {}

    /**
     * Validate coupon for frontend (subscription checkout).
     * POST /api/v1/validate-coupon
     * Body: code (required), package_id (optional), amount (optional - uses package price if package_id provided)
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:64',
            'package_id' => 'nullable|exists:subscription_packages,id',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $code = $request->input('code');
        $packageId = $request->filled('package_id') ? (int) $request->package_id : null;
        $amount = $request->input('amount');

        if ($amount === null && $packageId) {
            $package = SubscriptionPackage::find($packageId);
            $amount = $package ? (float) $package->effective_price : 0;
        }

        if ($amount === null || $amount < 0) {
            return response()->json([
                'valid' => false,
                'message' => __('admin.coupons.invalid_code'),
            ], 422);
        }

        $result = $this->couponService->validate($code, (float) $amount, $packageId);

        if (!$result['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $result['message'] ?? __('admin.coupons.not_found_or_expired'),
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'discount_amount' => $result['discount_amount'],
            'final_amount' => $result['final_amount'],
            'message' => null,
        ]);
    }
}
