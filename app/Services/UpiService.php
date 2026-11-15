<?php

namespace App\Services;

class UpiService
{
    /**
     * Generate dynamic Indian UPI URI
     */
    public static function generateUpiUri(string $upiId, string $upiName, float $amount, string $orderNumber): string
    {
        $params = [
            'pa' => trim($upiId),
            'pn' => trim($upiName),
            'am' => number_format($amount, 2, '.', ''),
            'tr' => $orderNumber,
            'tn' => "Print_{$orderNumber}",
            'cu' => 'INR'
        ];

        return 'upi://pay?' . http_build_query($params);
    }

    /**
     * Generate 1-Click App deep-links for mobile browsers
     */
    public static function getAppDeepLinks(string $upiUri): array
    {
        $encoded = urlencode($upiUri);
        return [
            'phonepe' => "phonepe://pay?url={$encoded}",
            'gpay' => "tez://upi/pay?data={$encoded}",
            'paytm' => "paytmmp://pay?url={$encoded}",
            'bhim' => "bhim://pay?url={$encoded}",
            'generic' => $upiUri,
        ];
    }

    /**
     * Generate QR Code image URL via Google Chart API / QR Server fallback
     */
    public static function getQrCodeUrl(string $upiUri, int $size = 300): string
    {
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($upiUri);
    }
}

