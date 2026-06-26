<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF Rendering (Browsershot / headless Chromium)
    |--------------------------------------------------------------------------
    |
    | Reports are rendered to PDF with Spatie Browsershot, which drives a
    | headless Chromium through Puppeteer. Chromium gives full modern CSS
    | (flexbox, grid, web fonts) and native Arabic shaping / RTL.
    |
    | Leave the binary paths null to let Browsershot use the "node" / "npm" on
    | the system PATH and Puppeteer's own bundled Chromium. Set them explicitly
    | when the web server runs with a restricted PATH (e.g. Herd, FPM) or when
    | you want to pin a specific Chrome build.
    |
    */

    'node_binary' => env('PDF_NODE_BINARY'),

    'npm_binary' => env('PDF_NPM_BINARY'),

    'chrome_path' => env('PDF_CHROME_PATH'),

    'node_module_path' => env('PDF_NODE_MODULE_PATH'),

    /*
    | Run Chromium with --no-sandbox. Required when rendering as root inside a
    | container; leave false on a normal desktop / Herd setup.
    */
    'no_sandbox' => (bool) env('PDF_NO_SANDBOX', false),

    /*
    | Hard timeout (seconds) for a single render.
    */
    'timeout' => (int) env('PDF_TIMEOUT', 120),

];
