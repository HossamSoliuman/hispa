<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class DataEntryController extends Controller
{
    //
    public function index()
    {
        return view('owner.data-entry.index');
    }
}
