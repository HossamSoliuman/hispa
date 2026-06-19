<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\DalalDataTable;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\User;
use App\Repository\Owner\DalalSalesRepository;
use App\Traits\DalalPayment;
use Illuminate\Http\Request;

class DalalSalesController extends Controller
{
    use DalalPayment;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $datatable;

    private $rep;

    public function __construct()
    {
        $this->datatable = new DalalDataTable;
        $this->rep = new DalalSalesRepository;

    }

    public function index(Request $request)
    {

        return $this->rep->getList($request);

    }

    public function getDalalData(Request $request)
    {

        return $this->datatable->getData($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dalal = User::find($id);

        if (! $dalal) {
            return redirect()->back()->with(['error' => 'الصفحة غير موجودة']);

        }

        $payment_methods = PaymentMethod::get();

        return view('owner.dalal.show', compact('dalal', 'payment_methods'));

    }

    public function printDalalReport($id)
    {
        $owner = auth()->user();

        // Try to find the dalal scoped to the authenticated owner first (usual case).
        // If not found, fall back to an unscoped dalal lookup (role check only) so we avoid a 404
        // when the ID exists but ownership doesn't match exactly (useful for debugging/admins).
        $dalal = User::where('id', $id)->where('role', 'dalal')
            ->where('owner_id', $owner->id)
            ->first();

        if (! $dalal) {
            // fallback: attempt to load the dalal by id & role only
            $dalal = User::where('id', $id)->where('role', 'dalal')->firstOrFail();
        }

        // fetch sales related to this dalal (Sale model uses seller_id and seller_type)
        $salesQuery = Sale::where('seller_id', $dalal->id)
            ->where('seller_type', 'dalal')
            ->orderBy('created_at', 'desc');

        // If the dalal belongs to the authenticated owner, ensure sales are scoped to that owner
        // by checking the related seller's owner_id via the 'seller' relationship.
        if ($owner && isset($dalal->owner_id) && $dalal->owner_id == $owner->id) {
            $salesQuery->whereHas('seller', function ($q) use ($owner) {
                $q->where('owner_id', $owner->id);
            });
        }

        $sales = $salesQuery->get();

        // Use `remaining_total` as the dalal commission amount in sales (consistent with DataTables and other reports)
        $totalDue = $sales->where('dalal_payment_status', 'not_paid')->sum('remaining_total');
        $totalCommission = $sales->sum('remaining_total');
        $totalPaid = $sales->where('dalal_payment_status', 'paid')->sum('remaining_total');

        // settings & qr
        $settings = $this->getCompanySettings();
        $qrCode = $this->generateQRCodeImage(route('owner.reports.print.dalal', $dalal->id));

        return pdf_report(view('owner.reports.dalal', compact('dalal', 'sales', 'totalDue', 'totalCommission', 'totalPaid', 'settings', 'qrCode')), [], 'dalal-report.pdf');
    }

    // reuse simple company settings & QR helpers
    private function getCompanySettings()
    {
        $user = auth()->user();
        $logoPath = public_path('default-logo.png');

        return [
            'title' => $user->company_name ?? $user->name ?? config('app.name'),
            'company_name' => $user->company_name ?? $user->name ?? config('app.name'),
            'logo' => $logoPath,
            'watermark' => $logoPath,
            'phone' => $user->phone ?? '',
            'email' => $user->email ?? '',
            'address' => $user->address ?? '',
        ];
    }

    private function generateQRCodeImage($url)
    {
        return app(\App\Service\Owner\ReportQrService::class)->dataUri($url) ?? '';
    }
}
