<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\SubscriptionPackage;
use Illuminate\Contracts\View\View;

class LandingPageController extends Controller
{
    /**
     * Site landing page (new Tailwind design from site/).
     */
    public function index(): View
    {
        $subscriptionPackages = SubscriptionPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('site.home', compact('subscriptionPackages'));
    }

    /**
     * About page.
     */
    public function about(): View
    {
        return view('site.about');
    }

    /**
     * Pricing page.
     */
    public function pricing(): View
    {
        $subscriptionPackages = SubscriptionPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('site.pricing', compact('subscriptionPackages'));
    }

    /**
     * Order review page (step 1 of checkout).
     */
    public function orderReview(): View
    {
        $subscriptionPackages = SubscriptionPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('site.order-review', compact('subscriptionPackages'));
    }

    /**
     * Payment page (step 2).
     */
    public function payment(): View
    {
        return view('site.payment');
    }

    /**
     * Processing / success page (step 3).
     */
    public function processing(): View
    {
        return view('site.processing');
    }

    /**
     * Contact page.
     */
    public function contact(): View
    {
        return view('site.contact');
    }

    public function comingSoon(): View
    {
        return view('landing-page.coming_soon');
    }

    public function roles(): View
    {
        $pages = Page::Active()->get();

        return view('landing-page.roles', compact('pages'));
    }
}
