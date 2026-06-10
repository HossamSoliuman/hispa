<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MonthClosing;
use App\Service\Owner\MonthClosingService;
use App\Service\Owner\ReportQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthClosingController extends Controller
{
    public function __construct(private MonthClosingService $service) {}

    public function index(Request $request)
    {
        $ownerId = $this->ownerId();

        $closings = MonthClosing::where('owner_id', $ownerId)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        return view('owner.month_closing.index', compact('closings', 'year', 'month'));
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|between:1,12',
        ]);

        $preview = $this->service->preview($this->ownerId(), (int) $data['year'], (int) $data['month']);

        return view('owner.month_closing.preview', [
            'preview' => $preview,
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
        ]);
    }

    public function close(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|between:1,12',
        ]);

        $ownerId = $this->ownerId();

        try {
            $closing = $this->service->close($ownerId, (int) $data['year'], (int) $data['month'], $ownerId);
        } catch (\DomainException $e) {
            return redirect()->route('owner.month-closing.index')->with('error', $e->getMessage());
        }

        return redirect()->route('owner.month-closing.show', $closing)
            ->with('success', __('owner.month_closing.messages.closed_success'));
    }

    public function show(MonthClosing $monthClosing)
    {
        $this->authorizeOwner($monthClosing);
        $monthClosing->load('dues');

        return view('owner.month_closing.show', ['closing' => $monthClosing]);
    }

    public function print(MonthClosing $monthClosing)
    {
        $this->authorizeOwner($monthClosing);
        $monthClosing->load('dues');
        $settings = $this->companySettings();

        return view('owner.month_closing.print', ['closing' => $monthClosing, 'settings' => $settings]);
    }

    public function reopen(MonthClosing $monthClosing)
    {
        $this->authorizeOwner($monthClosing);

        try {
            $this->service->reopen($monthClosing);
        } catch (\DomainException $e) {
            return redirect()->route('owner.month-closing.index')->with('error', $e->getMessage());
        }

        return redirect()->route('owner.month-closing.index')
            ->with('success', __('owner.month_closing.messages.reopened_success'));
    }

    private function ownerId(): int
    {
        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        return (int) $ownerId;
    }

    private function authorizeOwner(MonthClosing $monthClosing): void
    {
        abort_if($monthClosing->owner_id !== $this->ownerId(), 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function companySettings(): array
    {
        $owner = Auth::guard('owner')->user();
        $companyName = $owner->name ?? 'N/A';
        $vat = $owner->vat_number ?? '';

        return [
            'name' => $companyName,
            'company_name' => $companyName,
            'phone' => $owner->phone ?? 'N/A',
            'email' => $owner->email ?? 'N/A',
            'address' => $owner->address ?? 'N/A',
            'logo' => $owner->logo ?? null,
            'qr_code' => app(ReportQrService::class)->dataUri("Company: {$companyName}\nVAT: {$vat}"),
        ];
    }
}
