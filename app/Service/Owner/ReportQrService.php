<?php

namespace App\Service\Owner;

/**
 * Single source for report QR codes.
 *
 * Historically each print controller fetched a PNG synchronously from
 * api.qrserver.com (5s timeout) at page render — slow online, broken offline.
 * This service never performs an external HTTP request in the request cycle:
 * it generates the QR locally when a QR library is available, otherwise it
 * returns null and the report layout simply omits the QR.
 */
class ReportQrService
{
    /**
     * Return a `data:` URI for the QR, or null when no local generator exists.
     */
    public function dataUri(string $payload): ?string
    {
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(200)
                ->generate($payload);

            return 'data:image/svg+xml;base64,'.base64_encode((string) $svg);
        }

        if (class_exists(\Endroid\QrCode\Builder\Builder::class)) {
            $result = \Endroid\QrCode\Builder\Builder::create()
                ->data($payload)
                ->size(200)
                ->build();

            return $result->getDataUri();
        }

        return null;
    }
}
