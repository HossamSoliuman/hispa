<?php

namespace Tests\Feature\Owner;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class ReportDateDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_the_default_locale(): void
    {
        $localizedUrl = LaravelLocalization::getLocalizedURL('ar', route('landing-page'));

        $this->get('/')->assertRedirect($localizedUrl);
    }

    public function test_landing_page_renders_without_localization_redirects(): void
    {
        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
        ])->get(route('landing-page'))
            ->assertOk()
            ->assertViewIs('site.home');
    }
}
