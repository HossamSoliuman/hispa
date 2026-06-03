<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class FinancialReportsController extends Controller
{
    //
    public function index()
    {
        return view('owner.financial-reports.index');
    }
}
