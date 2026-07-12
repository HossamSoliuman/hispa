<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Admin reports landing hub: groups every platform-wide report into the same
 * categories the owner panel uses (operational / sales & revenue / inventory /
 * platform), so the admin has one entry point to every printable report.
 */
class ReportsHubController extends Controller
{
    public function index(): View
    {
        return view('admin.report.hub');
    }
}
