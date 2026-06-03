<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class CrewCheckController extends Controller
{
    public function index()
    {
        return view('owner.crew-check.index');
    }
}
