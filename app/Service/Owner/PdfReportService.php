<?php

namespace App\Service\Owner;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

/**
 * Renders an owner report blade view to a self-contained, printable HTML page.
 *
 * Owner reports are no longer rasterised to a PDF on the server. Instead the
 * report view (built on <x-report-layout>) is returned as a normal HTML page
 * that the user previews on screen in an A4 layout and then prints from the
 * browser ("Print", or "Save as PDF" from the print dialog). This keeps no PDF
 * engine / headless browser in the request path and frees the reports to use
 * the full modern CSS feature set, with Arabic shaping / RTL handled natively
 * by the browser.
 */
class PdfReportService
{
    /**
     * Render a report view (or pre-built View instance) to an HTML response.
     *
     * @param  array<string, mixed>  $data
     */
    public function printable(View|string $view, array $data = []): Response
    {
        $html = $view instanceof View ? $view->render() : view($view, $data)->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
