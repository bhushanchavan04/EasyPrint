<?php

namespace App\Services;

use App\Core\Env;

class RazorpayService
{
    private string $keyId;
    private string $keySecret;

    public function __construct()
    {
        $this->keyId = Env::get('RAZORPAY_KEY_ID', 'rzp_test_YourKeyIdHere');
        $this->keySecret = Env::get('RAZORPAY_KEY_SECRET', 'YourKeySecretHere');
    }

    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * Create Razorpay Order
     */
    public function createOrder(float $amountInRupees, string $receiptId, array $notes = []): array
    {
        $amountInPaise = (int)round($amountInRupees * 100);

        // If in test mode or keys not configured, return simulated order
        if (str_contains($this->keyId, 'YourKeyIdHere') || empty($this->keySecret)) {
            return [
                'id' => 'order_sim_' . bin2hex(random_bytes(8)),
                'entity' => 'order',
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => $receiptId,
                'status' => 'created',
                'simulated' => true
            ];
        }

        $url = 'https://api.razorpay.com/v1/orders';
        $data = [
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => $receiptId,
            'notes' => $notes,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->keyId}:{$this->keySecret}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        throw new \RuntimeException("Razorpay Order Creation Failed: {$response}");
    }

    /**
     * Verify payment signature
     */
    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        if (str_starts_with($orderId, 'order_sim_')) {
            return true; // Auto-pass simulated development orders
        }

        $expectedSignature = hash_hmac('sha256', "{$orderId}|{$paymentId}", $this->keySecret);
        return hash_equals($expectedSignature, $signature);
    }
}

