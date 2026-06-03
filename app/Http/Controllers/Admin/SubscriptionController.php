<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GrantTrialSubscriptionRequest;
use App\Http\Requests\Admin\RenewSubscriptionRequest;
use App\Http\Requests\Admin\StoreSubscriptionRequest;
use App\Http\Requests\Admin\SuspendSubscriptionRequest;
use App\Http\Requests\Admin\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of subscriptions with filters.
     */
    public function index(Request $request): View
    {
        $query = Subscription::with(['user', 'package']);

        $query = $this->applyFilters($query, $request);
        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);
        $counts = $this->subscriptionService->getCounts();

        return view('admin.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'activeCount' => $counts['activeCount'],
            'expiredCount' => $counts['expiredCount'],
            'trialCount' => $counts['trialCount'],
            'suspendedCount' => $counts['suspendedCount'],
        ]);
    }

    /**
     * Show the form for creating a new subscription.
     */
    public function create(): View
    {
        $packages = SubscriptionPackage::where('is_active', true)->orderBy('sort_order')->get();
        $fishermen = User::where('role', 'owner')->orderBy('name')->get();

        return view('admin.subscriptions.create', compact('packages', 'fishermen'));
    }

    /**
     * Store a newly created subscription.
     */
    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        $this->subscriptionService->create($request->validated());

        return redirect()->route('admin.subscriptions.index')
            ->with('success', __('admin.subscriptions.created_successfully'));
    }

    /**
     * Display the specified subscription with history and invoices.
     */
    public function show(Subscription $subscription): View
    {
        $subscription->load(['user', 'package', 'invoices']);
        $subscriptionHistory = Subscription::where('user_id', $subscription->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.subscriptions.show', compact('subscription', 'subscriptionHistory'));
    }

    /**
     * Show the form for editing the specified subscription.
     */
    public function edit(Subscription $subscription): View
    {
        $subscription->load('package');
        $packages = SubscriptionPackage::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.subscriptions.edit', compact('subscription', 'packages'));
    }

    /**
     * Update the specified subscription.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $subscription->update($request->validated());

        return redirect()->route('admin.subscriptions.index')
            ->with('success', __('admin.subscriptions.updated_successfully'));
    }

    /**
     * Remove the specified subscription.
     */
    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', __('admin.subscriptions.deleted_successfully'));
    }

    /**
     * Suspend (freeze) the subscription manually.
     */
    public function suspend(SuspendSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $this->subscriptionService->suspend(
            $subscription,
            $request->validated('suspension_reason')
        );

        return redirect()->back()
            ->with('success', __('admin.subscriptions.suspended_successfully'));
    }

    /**
     * Unsuspend (unfreeze) the subscription.
     */
    public function unsuspend(Subscription $subscription): RedirectResponse
    {
        $this->subscriptionService->unsuspend($subscription);

        return redirect()->back()
            ->with('success', __('admin.subscriptions.unsuspended_successfully'));
    }

    /**
     * Manually renew the subscription (extend end date by package duration).
     */
    public function renew(RenewSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $this->subscriptionService->renew(
            $subscription,
            $request->validated('duration_type')
        );

        return redirect()->back()
            ->with('success', __('admin.subscriptions.renewed_successfully'));
    }

    /**
     * Grant free trial.
     */
    public function grantTrial(GrantTrialSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $this->subscriptionService->grantTrial($subscription, $request->validated('trial_days'));

        return redirect()->back()
            ->with('success', __('admin.subscriptions.trial_granted_successfully'));
    }

    /**
     * Apply index filters from request.
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 'active')
                    ->where('is_suspended', false)
                    ->where('end_date', '>=', Carbon::today());
            } elseif ($request->status === 'expired') {
                $query->where(function ($q) {
                    $q->where('status', 'expired')
                        ->orWhere(function ($q2) {
                            $q2->where('status', 'active')->where('end_date', '<', Carbon::today());
                        });
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->boolean('suspended')) {
            $query->where('is_suspended', true);
        }

        return $query;
    }
}
