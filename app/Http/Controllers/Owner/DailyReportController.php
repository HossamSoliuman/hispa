<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class DailyReportController extends Controller
{
    public function index()
    {
        return view('owner.daily-report.index');
    }
}
