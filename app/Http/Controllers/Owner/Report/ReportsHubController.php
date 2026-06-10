<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;

/**
 * Reports landing hub (plan §4.1): groups every owner report by the client's
 * categories — تشغيلية / مالية / البحارة / إدارية / سنوية.
 */
class ReportsHubController extends Controller
{
    public function index()
    {
        return view('owner.report.hub');
    }
}
