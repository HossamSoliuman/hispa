<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public const DURATION_MONTHLY = 'monthly';
    public const DURATION_QUARTERLY = 'quarterly';
    public const DURATION_YEARLY = 'yearly';

    /**
     * Calculate end date from start date and duration type.
     */
    public function calculateEndDate(Carbon $startDate, string $durationType): Carbon
    {
        return match ($durationType) {
            self::DURATION_MONTHLY => $startDate->copy()->addMonth(),
            self::DURATION_QUARTERLY => $startDate->copy()->addMonths(3),
            self::DURATION_YEARLY => $startDate->copy()->addYear(),
            default => $startDate->copy()->addMonth(),
        };
    }

    /**
     * Create a new subscription with validated data.
     */
    public function create(array $validated): Subscription
    {
        $package = SubscriptionPackage::findOrFail($validated['package_id']);
        $durationType = $validated['duration_type'] ?? $package->duration_type;
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = $this->calculateEndDate($startDate, $durationType);
        $isTrial = (bool) ($validated['is_trial'] ?? false);
        $status = $isTrial ? 'trial' : 'active';
        $trialEndsAt = null;

        if ($status === 'trial' && !empty($validated['trial_days'])) {
            $trialEndsAt = $startDate->copy()->addDays((int) $validated['trial_days']);
        }

        return Subscription::create([
            'user_id' => $validated['user_id'],
            'package_id' => $validated['package_id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'trial_ends_at' => $trialEndsAt,
        ]);
    }

    /**
     * Manually renew subscription: extend end_date by package duration from current end_date.
     */
    public function renew(Subscription $subscription, ?string $durationType = null): Subscription
    {
        $subscription->load('package');
        $durationType = $durationType ?? $subscription->package?->duration_type ?? self::DURATION_MONTHLY;
        $baseDate = $subscription->end_date->isFuture() ? $subscription->end_date : Carbon::today();
        $newEndDate = $this->calculateEndDate($baseDate, $durationType);

        return DB::transaction(function () use ($subscription, $newEndDate) {
            $subscription->update([
                'end_date' => $newEndDate,
                'status' => 'active',
                'is_suspended' => false,
                'suspended_at' => null,
                'suspension_reason' => null,
                'renewal_count' => ($subscription->renewal_count ?? 0) + 1,
            ]);
            return $subscription->fresh();
        });
    }

    /**
     * Suspend (freeze) subscription.
     */
    public function suspend(Subscription $subscription, ?string $reason = null): Subscription
    {
        $subscription->update([
            'is_suspended' => true,
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ]);
        return $subscription->fresh();
    }

    /**
     * Unsuspend (unfreeze) subscription.
     */
    public function unsuspend(Subscription $subscription): Subscription
    {
        $status = $subscription->end_date >= Carbon::today() ? 'active' : 'expired';
        $subscription->update([
            'is_suspended' => false,
            'status' => $status,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);
        return $subscription->fresh();
    }

    /**
     * Grant trial: set status to trial and extend by trial_days.
     */
    public function grantTrial(Subscription $subscription, int $trialDays): Subscription
    {
        $trialEndsAt = Carbon::now()->addDays($trialDays);
        $subscription->update([
            'status' => 'trial',
            'trial_ends_at' => $trialEndsAt,
            'start_date' => Carbon::now(),
            'end_date' => $trialEndsAt,
        ]);
        return $subscription->fresh();
    }

    /**
     * Get subscription counts for filters (active, expired, trial, suspended).
     */
    public function getCounts(): array
    {
        $activeCount = Subscription::where('status', 'active')
            ->where('is_suspended', false)
            ->where('end_date', '>=', Carbon::today())
            ->count();

        $expiredCount = Subscription::where(function ($q) {
            $q->where('status', 'expired')
                ->orWhere(function ($q2) {
                    $q2->where('status', 'active')->where('end_date', '<', Carbon::today());
                });
        })->count();

        $trialCount = Subscription::where('status', 'trial')->count();
        $suspendedCount = Subscription::where('is_suspended', true)->count();

        return compact('activeCount', 'expiredCount', 'trialCount', 'suspendedCount');
    }
}
