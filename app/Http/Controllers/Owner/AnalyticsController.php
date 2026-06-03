<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('owner.analytics.index');
    }
}
