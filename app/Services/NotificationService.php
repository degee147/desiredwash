<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    // ─── Notification type constants (mirror Flutter enum) ────────────────────
    const TYPE_ORDER_CONFIRMED = 'order_confirmed';
    const TYPE_ORDER_PICKED_UP = 'order_picked_up';
    const TYPE_ORDER_READY = 'order_ready';
    const TYPE_ORDER_DELIVERED = 'order_delivered';
    const TYPE_ORDER_CANCELLED = 'order_cancelled';
    const TYPE_PAYMENT_SUCCESS = 'payment_success';
    const TYPE_PAYMENT_FAILED = 'payment_failed';
    const TYPE_WALLET_TOPUP = 'wallet_topup';
    const TYPE_GENERAL = 'general';

    // ─── Public helpers ───────────────────────────────────────────────────────

    /**
     * Persist a notification row AND fire a push to all user devices.
     */
    public function send(
        int $userId,
        string $title,
        string $body,
        string $type = self::TYPE_GENERAL,
        ?int $orderId = null,
    ): Notification {
        // 1. Persist to DB so it shows in the in-app notifications list
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'order_id' => $orderId,
            'is_read' => false,
        ]);

        // 2. Push via FCM (best-effort — failures are logged, not thrown)
        $this->pushToUser($userId, $title, $body, $type, $orderId);

        return $notification;
    }

    // ─── Convenience wrappers ─────────────────────────────────────────────────

    public function orderConfirmed(int $userId, string $orderId, string $orderRef): void
    {
        $this->send(
            $userId,
            '🧺 Pickup Confirmed!',
            "Your laundry pickup #{$orderRef} has been confirmed. We'll be there soon!",
            self::TYPE_ORDER_CONFIRMED,
            null, // order_id FK is int; pass null to avoid UUID cast issues — deep-link via data payload
        );
    }

    public function paymentSuccess(int $userId, float $amount, string $orderRef): void
    {
        $fmt = '₦' . number_format($amount, 0, '.', ',');
        $this->send(
            $userId,
            '✅ Payment Successful',
            "Your payment of {$fmt} for order #{$orderRef} was received.",
            self::TYPE_PAYMENT_SUCCESS,
        );
    }

    public function walletTopup(int $userId, float $amount): void
    {
        $fmt = '₦' . number_format($amount, 0, '.', ',');
        $this->send(
            $userId,
            '💰 Wallet Topped Up',
            "Your wallet has been credited with {$fmt}. Happy washing!",
            self::TYPE_WALLET_TOPUP,
        );
    }

    public function orderCancelled(int $userId, string $orderRef, bool $refunded = false): void
    {
        $body = "Your order #{$orderRef} has been cancelled.";
        if ($refunded) {
            $body .= ' A refund has been issued to your wallet.';
        }
        $this->send($userId, '❌ Order Cancelled', $body, self::TYPE_ORDER_CANCELLED);
    }

    // ─── FCM push dispatch ────────────────────────────────────────────────────

    private function pushToUser(
        int $userId,
        string $title,
        string $body,
        string $type,
        ?int $orderId,
    ): void {
        $tokens = UserDevice::where('user_id', $userId)
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens))
            return;

        foreach ($tokens as $token) {
            $this->sendFcmMessage($token, $title, $body, $type, $orderId);
        }
    }

    private function sendFcmMessage(
        string $token,
        string $title,
        string $body,
        string $type,
        ?int $orderId,
    ): void {
        $serviceAccountPath = base_path(config('services.firebase.credentials', 'firebase-service-account.json'));

        if (!file_exists($serviceAccountPath)) {
            Log::warning('FCM: service account file not found', ['path' => $serviceAccountPath]);
            return;
        }

        try {
            $accessToken = $this->getFirebaseAccessToken($serviceAccountPath);

            $projectId = json_decode(file_get_contents($serviceAccountPath), true)['project_id'] ?? null;

            if (!$projectId) {
                Log::error('FCM: could not read project_id from service account');
                return;
            }

            $data = ['type' => $type];
            if ($orderId)
                $data['order_id'] = (string) $orderId;

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                    'android' => [
                        'notification' => [
                            'channel_id' => 'desiredwash_orders',
                            'priority' => 'HIGH',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'alert' => ['title' => $title, 'body' => $body],
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

            if ($response->failed()) {
                Log::warning('FCM send failed', [
                    'token' => substr($token, 0, 20) . '...',
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                // Clean up stale / unregistered tokens
                if (in_array($response->status(), [400, 404])) {
                    UserDevice::where('fcm_token', $token)->delete();
                }
            } else {
                Log::info('FCM sent', ['token' => substr($token, 0, 20) . '...']);
            }
        } catch (\Throwable $e) {
            Log::error('FCM exception', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Exchange a Firebase service-account JSON for a short-lived OAuth2 access token.
     * Uses JWT RS256 — no extra library needed beyond the openssl PHP extension.
     */
    private function getFirebaseAccessToken(string $serviceAccountPath): string
    {
        $sa = json_decode(file_get_contents($serviceAccountPath), true);

        $now = time();
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = base64_encode(json_encode([
            'iss' => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signingInput = "{$header}.{$claims}";
        openssl_sign($signingInput, $signature, $sa['private_key'], 'SHA256');
        $jwt = $signingInput . '.' . base64_encode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json('access_token');
    }
}
