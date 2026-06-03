<?php

namespace App\Http\Controllers\Admin;

use App\DataTable\SalesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function __construct(
        private readonly SalesDataTable $salesDataTable
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Display sales index (admin layout – all sales).
     */
    public function index(): View
    {
        return view('admin.sales.index');
    }

    /**
     * Display the specified sale (admin layout).
     */
    public function show(string $id): View
    {
        $sale = Sale::with(['customer', 'paymentMethod', 'details', 'details.fish'])
            ->findOrFail($id);

        return view('admin.sales.show', compact('sale'));
    }

    /**
     * DataTable AJAX: all sales for admin.
     */
    public function getSalesData(Request $request): JsonResponse
    {
        return $this->salesDataTable->getData($request);
    }
}
