<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl = 'https://api.flutterwave.com/v3';

    public function __construct()
    {
        $this->secretKey = config('services.flutterwave.secret_key', '');
        $this->publicKey = config('services.flutterwave.public_key', '');
    }

    // ─── Virtual Account (wallet funding) ────────────────────────────────────

    /**
     * Create a permanent Flutterwave virtual account for a user.
     * Any transfer to this account credits the user's wallet via webhook.
     * Returns the virtual account data array or null on failure.
     */
    public function createVirtualAccount(
        string $email,
        string $name,
        string $txRef,
        string $phone = '',
    ): ?array {
        try {
            $nameParts = explode(' ', trim($name), 2);

            $payload = [
                'email'        => $email,
                'is_permanent' => true,
                'bvn'          => '22190239921',
                'tx_ref'       => $txRef,
                'currency'     => 'NGN',
                'phonenumber'  => $phone ?: '08000000000',
                'firstname'    => $nameParts[0],
                'lastname'     => $nameParts[1] ?? $nameParts[0],
                'narration'    => 'DesiredWash Wallet',
            ];

            $response = Http::withToken($this->secretKey)
                ->post("{$this->baseUrl}/virtual-account-numbers", $payload);

            if ($response->successful() && $response->json('status') === 'success') {
                return $response->json('data');
            }

            Log::error('Flutterwave virtual account creation failed', [
                'payload'  => $payload,
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Flutterwave virtual account HTTP error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    // ─── Payment Links ────────────────────────────────────────────────────────

    /**
     * Generate a Flutterwave payment link for an order.
     */
    public function createOrderPaymentLink(array $order, string $txRef): ?string
    {
        $payload = [
            'tx_ref'       => $txRef,
            'amount'       => $order['total'],
            'currency'     => 'NGN',
            'redirect_url' => "desiredwash://payment/success?tx_ref={$txRef}",
            'cancel_url'   => 'desiredwash://payment/cancel',
            'customer'     => [
                'email' => $order['user_email'],
                'name'  => $order['user_name'],
            ],
            'customizations' => [
                'title'       => 'DesiredWash Order',
                'description' => 'Laundry service payment',
                'logo'        => config('app.url') . '/logo.png',
            ],
        ];

        return $this->initiatePayment($payload);
    }

    /**
     * Generate a Flutterwave payment link for a wallet top-up.
     */
    public function createWalletTopupLink(int $userId, float $amount, string $txRef, string $email, string $name): ?string
    {
        $payload = [
            'tx_ref'       => $txRef,
            'amount'       => $amount,
            'currency'     => 'NGN',
            'redirect_url' => "desiredwash://wallet/success?tx_ref={$txRef}",
            'cancel_url'   => "desiredwash://wallet/cancel",
            'customer'     => [
                'email' => $email,
                'name'  => $name,
            ],
            'customizations' => [
                'title'       => 'DesiredWash Wallet',
                'description' => 'Wallet top-up',
                'logo'        => config('app.url') . '/logo.png',
            ],
        ];

        return $this->initiatePayment($payload);
    }

    /**
     * Initiate a payment and return the hosted link.
     */
    private function initiatePayment(array $payload): ?string
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->post("{$this->baseUrl}/payments", $payload);

            if ($response->successful() && $response->json('status') === 'success') {
                return $response->json('data.link');
            }

            Log::error('Flutterwave payment initiation failed', [
                'payload'  => $payload,
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Flutterwave HTTP error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    // ─── Verification ─────────────────────────────────────────────────────────

    /**
     * Verify a transaction by its ID server-side.
     */
    public function verifyTransaction(string $transactionId): ?array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/transactions/{$transactionId}/verify");

            if ($response->successful() && $response->json('status') === 'success') {
                return $response->json('data');
            }

            Log::error('Flutterwave verification failed', [
                'transaction_id' => $transactionId,
                'response'       => $response->json(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Flutterwave verification HTTP error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Query transactions by tx_ref to get the transaction ID.
     */
    public function getTransactionByRef(string $txRef): ?array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/transactions", ['tx_ref' => $txRef]);

            if ($response->successful() && $response->json('status') === 'success') {
                $data = $response->json('data');
                return is_array($data) && count($data) > 0 ? $data[0] : null;
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('Flutterwave query by ref error', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
