<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function index($slug)
    {

        $page = Page::where('slug', $slug)->firstOrFail();
        $pages = Page::Active()->get();

        return view('landing-page.page', compact('page', 'pages'));
    }
}
