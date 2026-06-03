<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubscriptionPackageRequest;
use App\Http\Requests\Admin\UpdateSubscriptionPackageRequest;
use App\Models\SubscriptionPackage;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request): View
    {
        $query = SubscriptionPackage::query()
            ->orderBy('sort_order')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name_ar', 'like', "%{$term}%")
                    ->orWhere('name_en', 'like', "%{$term}%");
            });
        }
        if ($request->filled('status') && in_array($request->status, ['0', '1'], true)) {
            $query->where('is_active', (int) $request->status);
        }

        $packages = $query->get();

        $totalPackages = SubscriptionPackage::count();
        $activePackages = SubscriptionPackage::where('is_active', true)->count();
        $inactivePackages = $totalPackages - $activePackages;

        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('is_suspended', false)
            ->where('end_date', '>=', now())
            ->count();
        $expiredSubscriptions = Subscription::where('status', 'expired')
            ->orWhere(fn ($q) => $q->where('status', 'active')->where('end_date', '<', now()))
            ->count();
        $trialSubscriptions = Subscription::where('status', 'trial')->count();
        $suspendedSubscriptions = Subscription::where('is_suspended', true)->count();

        return view('admin.subscription-packages.index', compact(
            'packages',
            'totalPackages',
            'activePackages',
            'inactivePackages',
            'totalSubscriptions',
            'activeSubscriptions',
            'expiredSubscriptions',
            'trialSubscriptions',
            'suspendedSubscriptions'
        ));
    }

    public function create(): View
    {
        return view('admin.subscription-packages.create');
    }

    public function store(StoreSubscriptionPackageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['feature_ar'] = $request->input('feature_ar', []);
        $data['feature_en'] = $request->input('feature_en', []);
        $data['is_featured'] = $request->boolean('is_featured');

        $data['price'] = $request->filled('price') && $request->input('price') !== ''
            ? $request->input('price')
            : null;

        if ($data['is_featured']) {
            SubscriptionPackage::where('id', '!=', 0)->update(['is_featured' => false]);
        }

        SubscriptionPackage::create($data);

        return redirect()
            ->route('admin.subscription-packages.index')
            ->with('success', __('admin.subscription_packages.created_successfully'));
    }

    public function show(SubscriptionPackage $subscriptionPackage): View
    {
        $subscriptionPackage->load('subscriptions.user');
        return view('admin.subscription-packages.show', compact('subscriptionPackage'));
    }

    public function edit(SubscriptionPackage $subscriptionPackage): View
    {
        return view('admin.subscription-packages.edit', compact('subscriptionPackage'));
    }

    public function update(UpdateSubscriptionPackageRequest $request, SubscriptionPackage $subscriptionPackage): RedirectResponse
    {
        $data = $request->validated();
        $data['feature_ar'] = $request->input('feature_ar', []);
        $data['feature_en'] = $request->input('feature_en', []);
        $data['is_featured'] = $request->boolean('is_featured');

        $data['price'] = $request->filled('price') && $request->input('price') !== ''
            ? $request->input('price')
            : null;

        if ($data['is_featured']) {
            SubscriptionPackage::where('id', '!=', $subscriptionPackage->id)->update(['is_featured' => false]);
        }

        $subscriptionPackage->update($data);

        return redirect()
            ->route('admin.subscription-packages.index')
            ->with('success', __('admin.subscription_packages.updated_successfully'));
    }

    public function destroy(SubscriptionPackage $subscriptionPackage): RedirectResponse
    {
        if ($subscriptionPackage->subscriptions()->count() > 0) {
            return redirect()
                ->route('admin.subscription-packages.index')
                ->with('error', __('admin.subscription_packages.cannot_delete_has_subscriptions'));
        }

        $subscriptionPackage->delete();

        return redirect()
            ->route('admin.subscription-packages.index')
            ->with('success', __('admin.subscription_packages.deleted_successfully'));
    }
}
