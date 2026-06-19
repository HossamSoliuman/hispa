<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\CustomerDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CustomerRequest;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new CustomerDataTable;
    }

    public function index()
    {
        return view('owner.customers.index');
    }

    public function getCustomerData(Request $request)
    {

        return $this->datatable->getData($request);
    }

    public function store(CustomerRequest $request)
    {

        try {
            DB::beginTransaction();

            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['phone'] = $request->phone;
            $data['notes'] = $request->notes;
            $data['owner_id'] = auth()->user()->id;
            $data['status'] = $request->status == 1 ? 1 : 0;
            $data['type'] = $request->type;

            $customer = Customer::create($data);
            DB::commit();
            session()->flash('success', 'تم اضافة البيانات بنجاح');

            return redirect()->route('owner.customers.index');

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }
    }

    public function update(CustomerRequest $request)
    {

        try {

            $id = $request->only('id');
            $customer = Customer::where('id', $id)->first();

            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['phone'] = $request->phone;
            $data['notes'] = $request->notes;
            $data['owner_id'] = auth()->user()->id;
            $data['status'] = $request->status == 1 ? 1 : 0;
            $data['type'] = $request->type;
            $customer->update($data);
            DB::commit();
            session()->flash('success', 'تم تحديث البيانات بنجاح');

            return redirect()->route('owner.customers.index');

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);

        }

    }

    public function destroy($id)
    {

        try {

            $customer = Customer::where('id', $id)->first();
            $customer->delete();

            DB::commit();
            session()->flash('success', 'تم حذف البيانات بنجاح');

            return response()->json(['message' => 'Data saved successfully'], 200);

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                session()->flash('error', 'حدث خطأ ما');
            }

            return response()->json(['message' => $ex->getMessage()], 403);

        }
    }

    public function printCustomersReport()
    {
        $owner = auth()->user();
        $customers = Customer::where('owner_id', $owner->id)
            ->withCount([
                'sales as total_sales' => function ($q) {
                    $q->select(DB::raw('COALESCE(SUM(remaining_total),0)'));
                },
                'sales',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCustomers = $customers->count();
        $activeCustomers = $customers->where('status', 1)->count();
        $totalRevenue = $customers->sum('total_sales');
        $totalOrders = $customers->sum('sales_count');

        // Get company settings
        $settings = $this->getCompanySettings();

        // Generate QR code
        $qrCode = $this->generateQRCodeImage(route('owner.customers.index'));

        return pdf_report(view('owner.reports.customers', compact('customers', 'owner', 'totalCustomers', 'activeCustomers', 'totalRevenue', 'totalOrders', 'settings', 'qrCode')), [], 'customers-report.pdf');
    }

    public function printSalesReport()
    {
        $owner = auth()->user();

        // Try to fetch sales for this owner (seller_id)
        try {
            $sales = Sale::where('seller_id', $owner->id)
                ->orderBy('sale_datetime', 'desc')
                ->get();
        } catch (\Throwable $e) {
            $sales = collect();
        }

        $totalRevenue = $sales->sum('total_price');

        // Get settings and QR code for header
        $settings = $this->getCompanySettings();
        $qrCode = $this->generateQRCodeImage(route('owner.reports.print.sales'));

        return pdf_report(view('owner.reports.customer-sales', compact('owner', 'sales', 'totalRevenue', 'settings', 'qrCode')), [], 'customer-sales.pdf');
    }

    /**
     * Get company settings for report header
     */
    private function getCompanySettings()
    {
        $user = auth()->user();

        // Get logo path - check if user has logo, otherwise use default
        $logoPath = null;
        if (! empty($user->logo)) {
            if (filter_var($user->logo, FILTER_VALIDATE_URL) || str_starts_with($user->logo, 'http')) {
                $urlPath = parse_url($user->logo, PHP_URL_PATH) ?: '';
                if (str_starts_with($urlPath, '/storage/')) {
                    $local = public_path(ltrim($urlPath, '/'));
                    if (file_exists($local)) {
                        $logoPath = $local;
                    }
                }

                if (! $logoPath) {
                    $possible = storage_path('app/public/'.ltrim(basename($urlPath), '/'));
                    if (file_exists($possible)) {
                        $logoPath = $possible;
                    }
                }
            } else {
                $logoPath = storage_path('app/public/'.ltrim($user->logo, '/'));
                if (! file_exists($logoPath)) {
                    $logoPath = public_path(ltrim($user->logo, '/'));
                }
            }
        }

        // Final fallback to default logo
        if (! $logoPath || ! file_exists($logoPath)) {
            $logoPath = public_path('default-logo.png');
        }

        $watermarkPath = public_path('default-logo.png');

        return [
            'title' => $user->company_name ?? $user->name ?? config('app.name'),
            'company_name' => $user->company_name ?? $user->name ?? config('app.name'),
            'logo' => $logoPath,
            'watermark' => $watermarkPath,
            'cr_number' => $user->cr_number ?? '',
            'phone' => $user->phone ?? '',
            'email' => $user->email ?? '',
            'address' => $user->address ?? '',
        ];
    }

    /**
     * Generate QR Code image as base64 data URL
     */
    private function generateQRCodeImage($url)
    {
        return app(\App\Service\Owner\ReportQrService::class)->dataUri($url) ?? '';
    }
}
