<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use Illuminate\Http\Request;

class BoatReportController extends Controller
{
    public function printBoatReport(Request $request, $boat_id = null)
    {
        $owner_id = auth()->id();

        $query = Boat::with(['captain', 'boat_type', 'maintenances', 'expenses'])
            ->where('owner_id', $owner_id);

        if ($boat_id) {
            $query->where('id', $boat_id);
        }

        $boats = $query->orderBy('name_en')->get();

        // compute additional statistics
        $totalMaintenanceCost = 0;
        $totalPayload = 0;
        foreach ($boats as $b) {
            $totalMaintenanceCost += (float) $b->maintenances->sum('cost');
            // some boats use 'payload' or 'cargo_capacity' - use payload if available
            $totalPayload += (float) ($b->payload ?? 0);
        }

        $statistics = [
            'total_boats' => $boats->count(),
            'active_boats' => $boats->where('status', 1)->count(),
            'total_maintenance_cost' => $totalMaintenanceCost,
            'total_payload' => $totalPayload,
        ];

        $settings = ownerCompanySettings();

        // QR payload and image using same TLV approach as other reports
        $qrPayload = [
            'seller_name' => $settings['title'] ?? 'حسبة',
            'timestamp' => now()->toIso8601String(),
            'total' => number_format((float) ($statistics['total_boats'] ?? 0), 2, '.', ''),
        ];

        $tlvBase64 = $this->generateQRCode($qrPayload);
        $qrCode = $this->generateQRCodeImage($tlvBase64);

        $filename = $boat_id
            ? 'boat-'.trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower((string) ($boats->first()?->name_en ?? $boats->first()?->name ?? $boat_id))), '-').'.pdf'
            : 'all-boats-report.pdf';

        return pdf_report(view('owner.reports.print.boat-report', compact('boats', 'statistics', 'settings', 'qrCode', 'boat_id')), [], $filename);
    }

    public function printAllBoatsReport(Request $request)
    {
        return $this->printBoatReport($request, null);
    }

    private function generateQRCodeImage($url)
    {
        return app(\App\Service\Owner\ReportQrService::class)->dataUri($url)
            ?? $this->generateQRPlaceholder($url);
    }

    private function generateQRPlaceholder($url)
    {
        $shortUrl = parse_url($url, PHP_URL_HOST).parse_url($url, PHP_URL_PATH);
        $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">'
            .'<rect width="200" height="200" fill="#f8f9fa" stroke="#e0e0e0" stroke-width="2"/>'
            .'<text x="100" y="100" font-family="Arial" font-size="12" text-anchor="middle" fill="#7f8c8d">QR Code</text>'
            .'<text x="100" y="120" font-family="Arial" font-size="8" text-anchor="middle" fill="#95a5a6">'.htmlspecialchars(substr($shortUrl, 0, 30)).'</text>'
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    private function generateQRCode($data)
    {
        $tlv = '';
        $tlv .= $this->encodeTLV(1, $data['seller_name'] ?? '');
        $tlv .= $this->encodeTLV(3, $data['timestamp'] ?? now()->toIso8601String());
        $tlv .= $this->encodeTLV(4, number_format((float) ($data['total'] ?? 0), 2, '.', ''));

        return base64_encode($tlv);
    }

    private function encodeTLV($tag, $value)
    {
        $valueBytes = $value;
        $length = strlen($valueBytes);

        return chr($tag).chr($length).$valueBytes;
    }
}
