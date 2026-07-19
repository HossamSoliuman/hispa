<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        foreach (array_keys(config('laravellocalization.supportedLocales', [])) as $locale) {
            foreach (['', '/about', '/pricing', '/contact', '/roles'] as $path) {
                $urls[] = [
                    'loc' => $this->localizedUrl($locale, $path),
                    'lastmod' => null,
                ];
            }
        }

        foreach (Page::query()->active()->get(['slug', 'updated_at']) as $page) {
            foreach (array_keys(config('laravellocalization.supportedLocales', [])) as $locale) {
                $urls[] = [
                    'loc' => $this->localizedUrl($locale, '/page/'.$page->slug),
                    'lastmod' => $page->updated_at?->utc()->toAtomString(),
                ];
            }
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function localizedUrl(string $locale, string $path): string
    {
        return rtrim(config('seo.site_url'), '/').'/'.$locale.$path;
    }
}
