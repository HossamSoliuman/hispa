<?php

namespace App\Traits;

use App\Imports\FishImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

trait FishImportTrait
{
    public function import(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'file' => 'required|max:2048',
        ]);

        Excel::queueImport(new FishImport, $request->file('file'));

        return back()->with('success', 'Fish  imported successfully.');
    }
}
