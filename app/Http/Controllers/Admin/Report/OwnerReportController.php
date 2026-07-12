<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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
    public function index()
    {
        $rows = $this->ownerRows();

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
     * Render the printable owners overview report (PDF). An optional
     * start_date/end_date pair keeps only owners whose current subscription
     * started within the range.
     */
    public function print(Request $request)
    {
        $from = $request->input('start_date');
        $to = $request->input('end_date');

        $rows = $this->ownerRows($from, $to);

        $totalOwners = $rows->count();
        $activeOwners = $rows->where('is_active', true)->count();
        $totalBoats = (int) $rows->sum('boats_used');
        $totalQuota = (int) $rows->sum('quota');

        $settings = $this->getCompanySettings();

        $filename = 'owners-report-'.($from ?? 'all').'-to-'.($to ?? 'all').'.pdf';

        return pdf_report(view('admin.report.owners_print', compact(
            'rows',
            'totalOwners',
            'activeOwners',
            'totalBoats',
            'totalQuota',
            'settings',
            'from',
            'to'
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
    private function ownerRows(?string $from = null, ?string $to = null): Collection
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

        return $rows;
    }

    /**
     * Get platform company settings for the report header (admin oversees all owners,
     * so these come from the global Setting table rather than a per-owner company).
     *
     * @return array<string, mixed>
     */
    private function getCompanySettings(): array
    {
        $companyName = Setting::where('key', 'site_name')->value('value') ?? config('app.name');

        return [
            'name' => $companyName,
            'title' => $companyName,
            'company_name' => $companyName,
            'address' => Setting::where('key', 'address')->value('value') ?? '',
            'phone' => Setting::where('key', 'phone')->value('value') ?? '',
            'email' => Setting::where('key', 'email')->value('value') ?? '',
            'logo' => Setting::where('key', 'logo')->value('value') ?? '',
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ];
    }
}
