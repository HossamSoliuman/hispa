<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BoatReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_boat_report', ['only' => ['index', 'show']]);
    }

    /**
     * On-screen platform-wide boats report (every owner's boats).
     */
    public function index(): View
    {
        $boats = Boat::with(['owner', 'boat_type', 'captain'])
            ->orderBy('id', 'desc')
            ->get();

        $totalBoats = $boats->count();
        $activeBoats = $boats->where('status', 1)->count();
        $ownersCovered = $boats->pluck('owner_id')->filter()->unique()->count();

        return view('admin.report.boats', compact(
            'boats',
            'totalBoats',
            'activeBoats',
            'ownersCovered'
        ));
    }

    /**
     * Render the printable boats report (PDF) reusing the shared owner boat-report
     * view. Admin oversees every owner, so no owner_id filter is applied; an
     * optional boat_id narrows the report to a single boat.
     */
    public function print(Request $request): Response
    {
        $boat_id = $request->input('boat_id');

        $query = Boat::with(['owner', 'captain', 'boat_type', 'maintenances', 'expenses']);

        if ($boat_id) {
            $query->where('id', $boat_id);
        }

        $boats = $query->orderBy('id', 'desc')->get();

        $totalMaintenanceCost = 0.0;
        $totalPayload = 0.0;
        foreach ($boats as $boat) {
            $totalMaintenanceCost += (float) $boat->maintenances->sum('cost');
            $totalPayload += (float) ($boat->payload ?? 0);
        }

        $statistics = [
            'total_boats' => $boats->count(),
            'active_boats' => $boats->where('status', 1)->count(),
            'total_maintenance_cost' => $totalMaintenanceCost,
            'total_payload' => $totalPayload,
        ];

        $settings = $this->getCompanySettings();
        $qrCode = $settings['qr_code'] ?? null;

        $filename = 'boats-report-'.($boat_id ?? 'all').'.pdf';

        return pdf_report(view('owner.reports.print.boat-report', array_merge(compact(
            'boats',
            'statistics',
            'settings',
            'qrCode',
            'boat_id'
        ), ['reportScope' => 'platform', 'showOwnerColumn' => true])), [], $filename);
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
