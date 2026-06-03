<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use App\Models\SubscriptionPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request): View
    {
        $query = Coupon::query()->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', '%' . $term . '%')
                    ->orWhere('name', 'like', '%' . $term . '%');
            });
        }
        if ($request->filled('status') && in_array($request->status, ['0', '1'], true)) {
            $query->where('is_active', (int) $request->status);
        }

        $coupons = $query->paginate(20)->withQueryString();

        $totalCoupons = Coupon::count();
        $activeCoupons = Coupon::where('is_active', true)->count();
        $inactiveCoupons = $totalCoupons - $activeCoupons;

        return view('admin.coupons.index', compact('coupons', 'totalCoupons', 'activeCoupons', 'inactiveCoupons'));
    }

    public function create(): View
    {
        $packages = SubscriptionPackage::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.coupons.create', compact('packages'));
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $data['package_ids'] = $request->filled('package_ids') ? $request->package_ids : null;
        Coupon::create($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', __('admin.coupons.created_successfully'));
    }

    public function show(Coupon $coupon): View
    {
        $coupon->load('invoices.subscription', 'invoices.user');
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon): View
    {
        $packages = SubscriptionPackage::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.coupons.edit', compact('coupon', 'packages'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $data['package_ids'] = $request->filled('package_ids') ? $request->package_ids : null;
        $coupon->update($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', __('admin.coupons.updated_successfully'));
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        if ($coupon->invoices()->count() > 0) {
            return redirect()->route('admin.coupons.index')
                ->with('error', __('admin.coupons.cannot_delete_has_usage'));
        }
        $coupon->delete();
        return redirect()->route('admin.coupons.index')
            ->with('success', __('admin.coupons.deleted_successfully'));
    }
}
