<?php

namespace App\Services;

use App\Mail\OrderStatusMail;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    // ─── Notification type constants (mirror Flutter enum) ───────────────────
    const TYPE_ORDER_CONFIRMED  = 'order_confirmed';
    const TYPE_ORDER_PICKED_UP  = 'order_picked_up';
    const TYPE_ORDER_READY      = 'order_ready';
    const TYPE_ORDER_DELIVERED  = 'order_delivered';
    const TYPE_ORDER_CANCELLED  = 'order_cancelled';
    const TYPE_PAYMENT_SUCCESS  = 'payment_success';
    const TYPE_PAYMENT_FAILED   = 'payment_failed';
    const TYPE_WALLET_TOPUP     = 'wallet_topup';
    const TYPE_GENERAL          = 'general';

    // ─── Core send ────────────────────────────────────────────────────────────

    /**
     * Persist a notification row AND fire a push to all user devices.
     */
    public function send(
        int     $userId,
        string  $title,
        string  $body,
        string  $type = self::TYPE_GENERAL,
        ?int    $orderId = null,
    ): Notification {
        // 1. Persist to DB
        $notification = Notification::create([
            'user_id'  => $userId,
            'title'    => $title,
            'body'     => $body,
            'type'     => $type,
            'order_id' => $orderId,
            'is_read'  => false,
        ]);

        // 2. Push via FCM (best-effort)
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
        );

        $this->sendOrderEmail($userId, $orderId, '🧺', 'Pickup Confirmed!',
            "Your laundry pickup #{$orderRef} has been confirmed. We'll be there soon!"
        );
    }

    public function orderPickedUp(int $userId, string $orderId, string $orderRef): void
    {
        $this->send(
            $userId,
            '🚗 Laundry Picked Up!',
            "We've collected your laundry for order #{$orderRef}. It's in good hands!",
            self::TYPE_ORDER_PICKED_UP,
        );

        $this->sendOrderEmail($userId, $orderId, '🚗', 'Laundry Picked Up!',
            "We've collected your laundry for order #{$orderRef}. It's in good hands!"
        );
    }

    public function orderReady(int $userId, string $orderId, string $orderRef): void
    {
        $this->send(
            $userId,
            '✨ Laundry Ready!',
            "Your laundry for order #{$orderRef} is clean and ready for delivery.",
            self::TYPE_ORDER_READY,
        );

        $this->sendOrderEmail($userId, $orderId, '✨', 'Laundry Ready for Delivery!',
            "Your laundry for order #{$orderRef} is clean, fresh, and ready for delivery."
        );
    }

    public function orderDelivered(int $userId, string $orderId, string $orderRef): void
    {
        $this->send(
            $userId,
            '🎉 Order Delivered!',
            "Your laundry for order #{$orderRef} has been delivered. Enjoy the freshness!",
            self::TYPE_ORDER_DELIVERED,
        );

        $this->sendOrderEmail($userId, $orderId, '🎉', 'Order Delivered!',
            "Your laundry for order #{$orderRef} has been delivered. Enjoy the freshness!"
        );
    }

    public function orderCancelled(int $userId, string $orderRef, bool $refunded = false): void
    {
        $body = "Your order #{$orderRef} has been cancelled.";
        if ($refunded) {
            $body .= ' A refund has been issued to your wallet.';
        }
        $this->send($userId, '❌ Order Cancelled', $body, self::TYPE_ORDER_CANCELLED);

        // For cancellation we don't have a direct orderId — skip order email
        // (push + in-app notification is sufficient)
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
        // Payment success is covered by the orderConfirmed email — no separate email.
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
        // Wallet top-up email is intentionally not sent (keep email for orders only).
    }

    // ─── Email for order updates ──────────────────────────────────────────────

    private function sendOrderEmail(
        int    $userId,
        string $orderId,
        string $emoji,
        string $statusTitle,
        string $statusMessage,
    ): void {
        try {
            $user  = User::find($userId);
            $order = Order::find($orderId);

            if (!$user || !$order) return;

            Mail::to($user->email)->send(new OrderStatusMail(
                user:          $user,
                order:         $order,
                statusTitle:   $statusTitle,
                statusMessage: $statusMessage,
                statusEmoji:   $emoji,
            ));
        } catch (\Throwable $e) {
            Log::warning('Order status email failed', [
                'user_id'  => $userId,
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    // ─── FCM push dispatch ────────────────────────────────────────────────────

    private function pushToUser(
        int     $userId,
        string  $title,
        string  $body,
        string  $type,
        ?int    $orderId,
    ): void {
        $tokens = UserDevice::where('user_id', $userId)->pluck('fcm_token')->toArray();
        if (empty($tokens)) return;

        foreach ($tokens as $token) {
            $this->sendFcmMessage($token, $title, $body, $type, $orderId);
        }
    }

    private function sendFcmMessage(
        string  $token,
        string  $title,
        string  $body,
        string  $type,
        ?int    $orderId,
    ): void {
        $serviceAccountPath = base_path(config('services.firebase.credentials', 'firebase-service-account.json'));

        if (!file_exists($serviceAccountPath)) {
            Log::warning('FCM: service account file not found', ['path' => $serviceAccountPath]);
            return;
        }

        try {
            $accessToken = $this->getFirebaseAccessToken($serviceAccountPath);
            $projectId   = json_decode(file_get_contents($serviceAccountPath), true)['project_id'] ?? null;

            if (!$projectId) {
                Log::error('FCM: could not read project_id from service account');
                return;
            }

            $data = ['type' => $type];
            if ($orderId) $data['order_id'] = (string) $orderId;

            $payload = [
                'message' => [
                    'token'        => $token,
                    'notification' => ['title' => $title, 'body' => $body],
                    'data'         => $data,
                    'android'      => [
                        'priority'     => 'high',
                        'notification' => ['channel_id' => 'desiredwash_orders'],
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
                    'token'  => substr($token, 0, 20) . '...',
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                if ($response->status() === 404) {
                    UserDevice::where('fcm_token', $token)->delete();
                }
            } else {
                Log::info('FCM sent', ['token' => substr($token, 0, 20) . '...']);
            }
        } catch (\Throwable $e) {
            Log::error('FCM exception', ['error' => $e->getMessage()]);
        }
    }

    private function getFirebaseAccessToken(string $serviceAccountPath): string
    {
        $sa     = json_decode(file_get_contents($serviceAccountPath), true);
        $now    = time();
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = base64_encode(json_encode([
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signingInput = "{$header}.{$claims}";
        openssl_sign($signingInput, $signature, $sa['private_key'], 'SHA256');
        $jwt = $signingInput . '.' . base64_encode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        return $response->json('access_token');
    }
}
