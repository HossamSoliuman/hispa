<?php

namespace App\Http\Controllers\Gov;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ComingSoonController extends Controller
{
    /**
     * Placeholder screen for every gov section that is not built yet. The
     * page title is derived from the current route name (gov.<key>) so a
     * single handler serves all "coming soon" links in the sidebar.
     */
    public function show(): View
    {
        $key = Str::after(request()->route()->getName(), 'gov.');

        return view('gov.coming-soon', [
            'pageTitle' => __('gov.menu.'.$key),
        ]);
    }
}
