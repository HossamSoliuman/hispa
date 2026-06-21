<?php

namespace App\Service\Owner;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

/**
 * Renders report blade views to a downloadable PDF using mPDF.
 *
 * Reports were previously opened as a separate HTML page and printed via the
 * browser. They now stream a real PDF download. mPDF is configured for Arabic
 * RTL (auto script/font detection) so the existing report layout renders with
 * correct shaping and direction.
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
        // Safety net: if mPDF is not installed yet, fall back to rendering the
        // report as an HTML page so reports keep working instead of erroring.
        if (! class_exists(Mpdf::class)) {
            return response($html);
        }

        $isRtl = app()->getLocale() === 'ar';

        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $tempDir,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        $mpdf->SetDirectionality($isRtl ? 'rtl' : 'ltr');
        $mpdf->showWatermarkImage = true;

        // Reports embed images (logo/watermark/QR) as base64 data URIs, so the
        // HTML easily exceeds PHP's default pcre.backtrack_limit of 1MB. mPDF
        // parses the whole document with PCRE and throws when that limit is hit,
        // so raise it for the duration of this render.
        ini_set('pcre.backtrack_limit', '5000000');
        ini_set('pcre.recursion_limit', '5000000');

        $mpdf->WriteHTML($html);

        return response(
            $mpdf->Output($filename, Destination::STRING_RETURN),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            ]
        );
    }
}
