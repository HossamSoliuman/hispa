<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\VendorsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\VendorRequest;
use App\Models\Expense;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class VendorsController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new VendorsDataTable;
    }

    public function index()
    {

        $totalVendors = User::where('role', 'vendor')
            ->where('owner_id', auth()->id())
            ->count();

        $activeVendors = User::where('role', 'vendor')
            ->where('owner_id', auth()->id())
            ->where('status', 1)
            ->count();

        $pendingAmount = Expense::where('status', 'pending')
            ->whereHas('vendor', function ($q) {
                $q->where('owner_id', auth()->id());
            })->sum('final_price');

        $totalExpenses = Expense::whereHas('vendor', function ($q) {
            $q->where('owner_id', auth()->id());
        })->sum('final_price');

        $totalPaid = Expense::whereHas('vendor', function ($q) {
            $q->where('owner_id', auth()->id());
        })->where('status', 'paid')->sum('final_price');

        $countExpenses = Expense::whereHas('vendor', function ($q) {
            $q->where('owner_id', auth()->id());
        })->count();

        $avgPerExpense = number_format($countExpenses > 0 ? $totalExpenses / $countExpenses : 0, 2);

        return view('owner.vendors.index', compact('totalVendors', 'activeVendors', 'pendingAmount', 'totalExpenses', 'avgPerExpense', 'totalPaid'));
    }

    public function getVendors(Request $request)
    {
        $vendors = User::where('role', 'vendor')
            ->where('owner_id', auth()->id())
            ->select('id', 'name', 'company_name', 'email', 'phone', 'status');

        if ($request->status !== null && $request->status !== '') {
            $vendors->where('status', $request->status);
        }

        if ($request->search) {
            $vendors->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('company_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        return $this->datatable->getData($vendors);
    }

    public function create()
    {
        $regions = Region::Active()->get();

        return view('owner.vendors.create', compact('regions'));
    }

    public function show($id)
    {
        $vendor = User::findOrFail($id);

        return view('owner.vendors.show', compact('vendor'));
    }

    public function store(VendorRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $data['role'] = 'vendor';
            $data['owner_id'] = auth()->user()->id;
            $user = User::create($data);
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return response()->json(['error' => $ex->getmessage()]);
            }

            return response()->json(['error' => 'حدث خطأ ما']);
        }
    }

    public function edit($id)
    {
        $vendor = User::findOrFail($id);
        $regions = Region::Active()->get();

        return view('owner.vendors.edit', compact('vendor', 'regions'));
    }

    public function update(VendorRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $vendor = User::findOrFail($id);
            $data = $request->validated();
            $vendor->update($data);
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return response()->json(['error' => $ex->getmessage()]);
            }

            return response()->json(['error' => 'حدث خطأ ما']);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $vendor = User::findOrFail($id);
            $vendor->delete();
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return response()->json(['error' => $ex->getmessage()]);
            }

            return response()->json(['error' => 'حدث خطأ ما']);
        }
    }

    public function printVendorReport($id)
    {
        $owner = auth()->user();
        $vendor = User::where('id', $id)->where('role', 'vendor')->where('owner_id', $owner->id)->firstOrFail();

        // fetch expenses related to this vendor for this owner
        $expenses = Expense::where('vendor_id', $vendor->id)
            ->whereHas('vendor', function ($q) use ($owner) {
                $q->where('owner_id', $owner->id);
            })->orderBy('created_at', 'desc')->get();

        $totalDue = $expenses->where('status', 'pending')->sum('final_price');
        $totalExpenses = $expenses->sum('final_price');

        // settings & qr
        $settings = $this->getCompanySettings();
        $qrCode = $this->generateQRCodeImage(route('owner.reports.print.vendor', $vendor->id));

        return pdf_report(view('owner.reports.vendor', compact('vendor', 'expenses', 'totalDue', 'totalExpenses', 'settings', 'qrCode')), [], 'vendor-report.pdf');
    }

    // reuse simple company settings & QR helpers
    private function getCompanySettings()
    {
        return ownerCompanySettings([
            'watermark' => public_path('default-logo.png'),
        ]);
    }

    private function generateQRCodeImage($url)
    {
        return app(\App\Service\Owner\ReportQrService::class)->dataUri($url) ?? '';
    }
}
