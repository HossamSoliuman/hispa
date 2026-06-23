<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\OwnerDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOwnerRequest;
use App\Http\Requests\Admin\UpdateOwnerRequest;
use App\Models\Governorate;
use App\Models\Invoice;
use App\Models\Port;
use App\Models\Region;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Services\CouponService;
use App\Services\Owner\OwnerMasterDataService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class OwnerController extends Controller
{
    public function __construct(
        private readonly OwnerDataTable $ownerDataTable,
        private readonly SubscriptionService $subscriptionService,
        private readonly CouponService $couponService,
        private readonly OwnerMasterDataService $masterDataService
    ) {}

    /**
     * Display a listing of owners (fishermen).
     */
    public function index(): View
    {
        return view('admin.owner.index');
    }

    /**
     * Show the form for creating a new owner (with optional subscription + cash payment).
     */
    public function create(): View
    {
        $regions = Region::query()->orderBy('name')->get();
        $governorates = Governorate::query()->orderBy('name')->get();
        $ports = Port::query()->orderBy('name')->get();
        $packages = SubscriptionPackage::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.owner.create', compact('regions', 'governorates', 'ports', 'packages'));
    }

    /**
     * Store a new owner (and optionally subscription + invoice paid cash to admin).
     */
    public function store(StoreOwnerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $owner = DB::transaction(function () use ($validated) {
            $owner = User::create([
                'name' => $validated['name'],
                'owner_type' => $validated['owner_type'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => 'owner',
                'status' => (int) ($validated['status'] ?? 1),
                'region_id' => $validated['region_id'] ?? null,
                'governorate_id' => $validated['governorate_id'] ?? null,
                'port_id' => $validated['port_id'] ?? null,
            ]);

            // Give the new owner their own isolated copy of the default master data.
            $this->masterDataService->seedFor($owner);

            if (! empty($validated['add_subscription']) && ! empty($validated['package_id']) && ! empty($validated['start_date'])) {
                $subscription = $this->subscriptionService->create([
                    'user_id' => $owner->id,
                    'package_id' => $validated['package_id'],
                    'start_date' => $validated['start_date'],
                ]);

                $package = $subscription->package;
                $amount = $package ? (float) $package->effective_price : 0;
                $discountAmount = 0;
                $couponId = null;

                if (! empty($validated['coupon_code'])) {
                    $result = $this->couponService->validate(
                        $validated['coupon_code'],
                        $amount,
                        (int) $validated['package_id']
                    );
                    if ($result['valid']) {
                        $discountAmount = $result['discount_amount'];
                        $this->couponService->applyUsage($result['coupon']);
                        $couponId = $result['coupon']->id;
                    }
                }

                $totalAmount = max(0, $amount - $discountAmount);

                Invoice::create([
                    'subscription_id' => $subscription->id,
                    'user_id' => $owner->id,
                    'coupon_id' => $couponId,
                    'amount' => $amount,
                    'vat_rate' => 0,
                    'vat_amount' => 0,
                    'total_amount' => $totalAmount,
                    'discount_amount' => $discountAmount,
                    'payment_method' => 'cash',
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'payment_confirmed_at' => now(),
                    'payment_confirmed_by' => auth('admin')->id(),
                    'payment_notes' => $validated['payment_notes'] ?? __('admin.owner.payment_cash_to_admin'),
                ]);
            }

            return $owner;
        });

        return redirect()
            ->route('admin.owner.show', $owner->id)
            ->with('success', __('admin.owner.created_successfully'));
    }

    /**
     * Return DataTables JSON data for owners listing (AJAX).
     */
    public function getOwnerData(Request $request): JsonResponse
    {
        return $this->ownerDataTable->getData($request);
    }

    /**
     * Resolve owner by id (must be role=owner).
     */
    private function findOwner(string $id): User
    {
        return User::where('role', 'owner')->findOrFail($id);
    }

    /**
     * Display the specified owner (profile, boats, subscriptions, sales, customers).
     */
    public function show(string $id): View
    {
        $owner = $this->findOwner($id);
        $owner->loadCount(['boats', 'subscriptions', 'trips', 'salesAsSeller', 'customers']);
        $owner->load([
            'boats' => fn ($q) => $q->orderBy('created_at', 'desc'),
            'subscriptions' => fn ($q) => $q->with('package')->orderBy('created_at', 'desc'),
            'activeSubscription.package',
            'trips' => fn ($q) => $q->orderBy('created_at', 'desc')->limit(50),
            'salesAsSeller' => fn ($q) => $q->with(['trip', 'customer'])->orderBy('created_at', 'desc')->limit(50),
            'customers' => fn ($q) => $q->orderBy('created_at', 'desc')->limit(50),
            'region',
            'governorate',
            'port',
        ]);

        return view('admin.owner.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified owner (profile + subscription).
     */
    public function edit(string $id): View
    {
        $owner = $this->findOwner($id);
        $owner->load(['activeSubscription.package', 'subscriptions.package', 'region', 'governorate']);
        $packages = SubscriptionPackage::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.owner.edit', compact('owner', 'packages'));
    }

    /**
     * Update the specified owner (profile and optionally current subscription).
     */
    public function update(UpdateOwnerRequest $request, string $id): RedirectResponse
    {
        $owner = $this->findOwner($id);

        $owner->update([
            'name' => $request->validated('name'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email'),
            'status' => (int) $request->validated('status'),
            'owner_type' => $request->validated('owner_type'),
        ]);

        if ($request->filled('subscription_id')) {
            $sub = $owner->subscriptions()->find($request->validated('subscription_id'));
            if ($sub) {
                $sub->update([
                    'package_id' => $request->validated('package_id'),
                    'start_date' => $request->validated('start_date'),
                    'end_date' => $request->validated('end_date'),
                    'status' => $request->validated('subscription_status'),
                ]);
            }
        }

        return redirect()
            ->route('admin.owner.show', $owner->id)
            ->with('success', __('admin.owner.updated_successfully'));
    }
}
