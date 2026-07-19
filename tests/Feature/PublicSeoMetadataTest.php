<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PublicSeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->withoutMiddleware([
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
            LocaleSessionRedirect::class,
        ]);
    }

    public function test_the_arabic_homepage_has_production_seo_metadata(): void
    {
        app()->setLocale('ar');

        $response = $this->get(route('landing-page'));

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="https://hisbah.huwat.net/ar" />', false);
        $response->assertSee('<link rel="alternate" hreflang="en" href="https://hisbah.huwat.net/en" />', false);
        $response->assertSee('<meta property="og:locale" content="ar_SA" />', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image" />', false);
        $response->assertSee('https://hisbah.huwat.net/site/assets/hisbah-huwat-logo.jpg', false);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('"@context":"https://schema.org"', false);
    }

    public function test_private_public_pages_are_not_indexed(): void
    {
        $response = $this->get(route('frontend.show_login_form'));

        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex, nofollow" />', false);
    }

    public function test_sitemap_lists_localized_public_pages_and_active_content_pages(): void
    {
        Page::query()->create([
            'title_ar' => 'عن حسبة',
            'title_en' => 'About Hesba',
            'body_ar' => 'محتوى الصفحة',
            'body_en' => 'Page content',
            'slug' => 'about-hesba',
            'status' => 1,
            'page_type' => 1,
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<loc>https://hisbah.huwat.net/ar</loc>', false);
        $response->assertSee('<loc>https://hisbah.huwat.net/en/page/about-hesba</loc>', false);
    }
}
