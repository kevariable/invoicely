<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF driver
    |--------------------------------------------------------------------------
    |
    | Which renderer GenerateInvoiceAction uses to turn the invoice blade
    | into a PDF.
    |
    | Supported drivers:
    |   - "browsershot" (default) — Spatie Browsershot + local Chromium /
    |     puppeteer. Requires `npm install` so node_modules/puppeteer is
    |     present, and a chromium binary on the host.
    |   - "cloudflare" — Cloudflare Browser Rendering REST API. No local
    |     Chromium needed; works on serverless hosts.
    |
    */

    'driver' => env('LARAVEL_PDF_DRIVER', 'browsershot'),

    'cloudflare' => [
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
        'api_token' => env('CLOUDFLARE_API_TOKEN'),
        'timeout' => (int) env('CLOUDFLARE_PDF_TIMEOUT', 60),
    ],

];
