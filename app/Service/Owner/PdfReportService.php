<?php

namespace App\Service\Owner;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Spatie\Browsershot\Browsershot;

/**
 * Renders report blade views to a downloadable PDF using Spatie Browsershot.
 *
 * Browsershot drives a headless Chromium (via Puppeteer), so reports render
 * with the exact fidelity of a real browser: modern CSS (flexbox, grid, web
 * fonts) and native Arabic shaping / RTL. This replaces the previous mPDF
 * engine, which could not handle the complex layouts the reports now use.
 */
class PdfReportService
{
    /**
     * Render a view (or pre-built View instance) to a PDF response.
     *
     * @param  array<string, mixed>  $data
     * @param  'inline'|'attachment'  $disposition  inline opens an in-browser preview; attachment forces a download
     */
    public function download(View|string $view, array $data = [], string $filename = 'report.pdf', string $disposition = 'attachment'): Response
    {
        $html = $view instanceof View ? $view->render() : view($view, $data)->render();

        return $this->fromHtml($html, $filename, $disposition);
    }

    /**
     * Build a PDF response from raw HTML.
     *
     * @param  'inline'|'attachment'  $disposition
     */
    public function fromHtml(string $html, string $filename = 'report.pdf', string $disposition = 'attachment'): Response
    {
        // Safety net: if Browsershot is not installed yet, fall back to rendering
        // the report as an HTML page so reports keep working instead of erroring.
        if (! class_exists(Browsershot::class)) {
            return response($html);
        }

        $pdf = $this->renderer($html)->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
        ]);
    }

    /**
     * Build a configured Browsershot instance for the given HTML document.
     *
     * Page margins are intentionally driven from the document's own CSS
     * (@page / the report layout) rather than overridden here, so each report
     * controls its own geometry. Background graphics (the black section bars,
     * KPI cells, etc.) are printed via showBackground().
     */
    private function renderer(string $html): Browsershot
    {
        $browsershot = Browsershot::html($html)
            ->format('A4')
            ->showBackground()
            ->emulateMedia('print')
            ->waitUntilNetworkIdle()
            ->timeout((int) config('pdf.timeout', 120));

        if ($nodeBinary = config('pdf.node_binary')) {
            $browsershot->setNodeBinary($nodeBinary);
        }

        if ($npmBinary = config('pdf.npm_binary')) {
            $browsershot->setNpmBinary($npmBinary);
        }

        if ($chromePath = config('pdf.chrome_path')) {
            $browsershot->setChromePath($chromePath);
        }

        $browsershot->setNodeModulePath(config('pdf.node_module_path') ?: base_path('node_modules'));

        if (config('pdf.no_sandbox')) {
            $browsershot->noSandbox();
        }

        return $browsershot;
    }
}
