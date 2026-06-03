<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Example controller demonstrating how to use report components
 * across different modules (Gov, Owner, Admin, etc.)
 */
class ReportComponentExampleController extends Controller
{
    /**
     * Example: Simple invoice report using components
     */
    public function simpleInvoice()
    {
        // Prepare company settings
        $settings = [
            'company_name' => 'شركة مثال / Example Company',
            'email' => 'info@example.com',
            'phone' => '+966 50 123 4567',
            'address' => 'الرياض، المملكة العربية السعودية',
            'logo' => null, // Will use default logo
        ];

        // Prepare invoice data
        $items = [
            (object) [
                'id' => 1,
                'name' => 'منتج أ / Product A',
                'quantity' => 10,
                'price' => 100.00,
                'total' => 1000.00,
            ],
            (object) [
                'id' => 2,
                'name' => 'منتج ب / Product B',
                'quantity' => 5,
                'price' => 200.00,
                'total' => 1000.00,
            ],
        ];

        // Calculate statistics
        $statistics = [
            'subtotal' => 2000.00,
            'tax' => 300.00,
            'total' => 2300.00,
            'total_items' => count($items),
        ];

        // Generate QR code URL
        $qrCode = route('example.invoice', ['id' => 12345]);

        return view('examples.simple-invoice-component', compact(
            'settings',
            'items',
            'statistics',
            'qrCode'
        ));
    }

    /**
     * Example: Custom report with additional sections
     */
    public function customReport(Request $request)
    {
        $settings = $this->getCompanySettings();

        // Your custom data
        $customData = collect([
            ['field1' => 'Value 1', 'field2' => 'Value 2', 'amount' => 500],
            ['field1' => 'Value 3', 'field2' => 'Value 4', 'amount' => 750],
        ]);

        $statistics = [
            'total_amount' => $customData->sum('amount'),
            'total_records' => $customData->count(),
        ];

        $fromDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $qrCode = route('example.custom-report', $request->only(['start_date', 'end_date']));

        return view('examples.custom-report-component', compact(
            'customData',
            'statistics',
            'settings',
            'qrCode',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Helper method to get company settings
     */
    private function getCompanySettings()
    {
        // This would typically fetch from database
        $user = auth()->guard('dalal')->user() ?? auth()->guard('gov')->user() ?? auth()->guard('owner')->user();

        return [
            'company_name' => $user->name ?? 'Company Name',
            'email' => $user->email ?? 'info@company.com',
            'phone' => '+966 50 123 4567',
            'address' => 'Saudi Arabia',
            'logo' => null,
        ];
    }
}
