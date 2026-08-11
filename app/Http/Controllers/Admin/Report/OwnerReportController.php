<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class OwnerReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_owner_report', ['only' => ['index', 'show']]);
    }

    /**
     * On-screen owners overview: every owner with their plan, boat quota usage
     * and subscription status across the whole platform.
     */
    public function index(Request $request): View
    {
        $rows = $this->ownerRows(status: $this->validatedStatus($request));

        $totalOwners = $rows->count();
        $activeOwners = $rows->where('is_active', true)->count();
        $totalBoats = (int) $rows->sum('boats_used');
        $totalQuota = (int) $rows->sum('quota');

        return view('admin.report.owners', compact(
            'rows',
            'totalOwners',
            'activeOwners',
            'totalBoats',
            'totalQuota'
        ));
    }

    /**
     * Render the printable owners overview report (PDF). Optional date and
     * status filters narrow the owners included in the report.
     */
    public function print(Request $request): Response
    {
        $from = $request->input('start_date');
        $to = $request->input('end_date');
        $status = $this->validatedStatus($request);

        $rows = $this->ownerRows($from, $to, $status);

        $totalOwners = $rows->count();
        $activeOwners = $rows->where('is_active', true)->count();
        $totalBoats = (int) $rows->sum('boats_used');
        $totalQuota = (int) $rows->sum('quota');

        $settings = $this->getCompanySettings();
        $generatedAt = now();
        $documentNumber = 'OWN-'.$generatedAt->format('Ymd-His');
        $appliedFilters = [
            'status' => $status,
            'from_date' => $from,
            'to_date' => $to,
            'generated_at' => $generatedAt,
        ];

        $filename = 'owners-report-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        return pdf_report(view('admin.report.owners_print', compact(
            'rows',
            'totalOwners',
            'activeOwners',
            'totalBoats',
            'totalQuota',
            'settings',
            'documentNumber',
            'appliedFilters'
        )), [], $filename);
    }

    /**
     * Build the per-owner display rows shared by the on-screen and printable
     * reports. Quota comes from the enforced boatLimit() (0 when no active
     * subscription); plan/status/dates fall back to the owner's most recent
     * subscription when none is currently active.
     *
     * @return Collection<int, object>
     */
    private function ownerRows(?string $from = null, ?string $to = null, ?string $status = null): Collection
    {
        $owners = User::ownerRole()
            ->with(['activeSubscription.package', 'subscriptions.package'])
            ->withCount('boats')
            ->orderBy('id', 'desc')
            ->get();

        $rows = $owners->map(function (User $owner): object {
            $active = $owner->activeSubscription;
            $current = $active ?: $owner->subscriptions->sortByDesc('id')->first();

            return (object) [
                'id' => $owner->id,
                'name' => $owner->name,
                'phone' => $owner->phone,
                'plan_name' => optional($current?->package)->name,
                'status' => $current?->status,
                'is_active' => (bool) $active,
                'boats_used' => (int) $owner->boats_count,
                'quota' => $owner->boatLimit(),
                'start_date' => $current?->start_date,
                'end_date' => $current?->end_date,
            ];
        });

        if ($from || $to) {
            $rows = $rows->filter(function (object $row) use ($from, $to): bool {
                if (! $row->start_date) {
                    return false;
                }

                $start = Carbon::parse($row->start_date);

                if ($from && $start->lt(Carbon::parse($from))) {
                    return false;
                }

                if ($to && $start->gt(Carbon::parse($to))) {
                    return false;
                }

                return true;
            })->values();
        }

        if ($status !== null) {
            $rows = $rows->where('status', $status)->values();
        }

        return $rows;
    }

    private function validatedStatus(Request $request): ?string
    {
        $status = $request->query('status');

        return is_string($status) && in_array($status, [
            'active',
            'pending',
            'trial',
            'expired',
            'suspended',
        ], true) ? $status : null;
    }

    /**
     * Get platform company settings for the report header (admin oversees all owners,
     * so these come from the global Setting table rather than a per-owner company).
     *
     * @return array<string, mixed>
     */
    private function getCompanySettings(): array
    {
        return adminReportCompanySettings();
    }
}
