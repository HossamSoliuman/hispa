<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class DocumentsController extends Controller
{
    public function index()
    {
        return view('owner.documents.index');
    }
}
