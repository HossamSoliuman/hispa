<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF Rendering (mPDF — pure PHP)
    |--------------------------------------------------------------------------
    |
    | Reports are rendered to PDF with mPDF, a pure-PHP engine. It needs no
    | headless browser / Node, so it runs on restricted shared hosting where a
    | real browser cannot launch. mPDF has first-class Arabic shaping + RTL and
    | embeds our own TTF fonts so the print matches the on-screen UI.
    |
    */

    /*
    | Writable scratch directory mPDF uses for its font cache and temp files.
    | Must be writable by the web server user in every environment.
    */
    'temp_dir' => env('PDF_TEMP_DIR', storage_path('app/mpdf')),

    /*
    | Directory holding the embedded TTF font files (Tajawal).
    */
    'font_dir' => env('PDF_FONT_DIR', resource_path('fonts')),

    /*
    | Default font family (must match a key registered in PdfReportService).
    */
    'default_font' => env('PDF_DEFAULT_FONT', 'tajawal'),

    /*
    | Page margins in millimetres.
    */
    'margins' => [
        'top' => (int) env('PDF_MARGIN_TOP', 12),
        'bottom' => (int) env('PDF_MARGIN_BOTTOM', 12),
        'left' => (int) env('PDF_MARGIN_LEFT', 10),
        'right' => (int) env('PDF_MARGIN_RIGHT', 10),
    ],

];
