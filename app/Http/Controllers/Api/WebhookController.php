<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWebhook;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * POST /api/v1/webhooks/flutterwave
     *
     * Entry point for all Flutterwave webhook events.
     *
     * Security:
     *   - Verified via the `verif-hash` header (set in Flutterwave dashboard → Webhooks → Secret Hash).
     *   - Route is excluded from CSRF (see bootstrap/app.php).
     *   - No auth:sanctum middleware — Flutterwave calls this server-to-server.
     *
     * Processing:
     *   - Payload is persisted immediately so we never lose data.
     *   - Actual credit/order-confirmation logic runs in a queued job so we
     *     return 200 to Flutterwave within milliseconds.
     *   - The job re-verifies the transaction server-side via Flutterwave's
     *     /v3/transactions/{id}/verify — we never trust the webhook payload alone.
     */
    public function handle(Request $request): Response
    {
        // ── 1. Signature verification ─────────────────────────────────────────
        $secret = config('services.flutterwave.secret_hash')
            ?: config('services.flutterwave.webhook_secret');

        if ($secret) {
            $receivedHash = $request->header('verif-hash');
            if (!$receivedHash || !hash_equals($secret, $receivedHash)) {
                Log::warning('Flutterwave webhook: invalid verif-hash', [
                    'ip'            => $request->ip(),
                    'received_hash' => $receivedHash ? substr($receivedHash, 0, 10) . '...' : 'none',
                ]);
                // Return 200 anyway — Flutterwave marks non-200 as failures and retries
                // A proper 401 would cause noise; we just silently discard.
                return response('Unauthorized', 200);
            }
        }

        // ── 2. Parse payload ──────────────────────────────────────────────────
        $payload = $request->all();
        $event   = $payload['event'] ?? null;
        $txData  = $payload['data']  ?? [];
        $txRef   = $txData['tx_ref'] ?? null;
        $txId    = $txData['id']     ?? null;

        Log::info('Flutterwave webhook received', [
            'event'  => $event,
            'tx_ref' => $txRef,
            'tx_id'  => $txId,
            'status' => $txData['status'] ?? null,
        ]);

        // ── 3. Filter — only process successful charges ───────────────────────
        if ($event !== 'charge.completed' || ($txData['status'] ?? null) !== 'successful') {
            Log::info('Flutterwave webhook: event ignored', ['event' => $event]);
            return response('Ignored', 200);
        }

        if (!$txRef || !$txId) {
            Log::warning('Flutterwave webhook: missing tx_ref or tx_id', ['payload' => $payload]);
            return response('Bad payload', 200);
        }

        // ── 4. Idempotency — skip if already queued or processed ─────────────
        $alreadyQueued = WebhookLog::where('tx_ref', $txRef)
            ->whereIn('status', ['pending', 'processing', 'processed'])
            ->exists();

        if ($alreadyQueued) {
            Log::info('Flutterwave webhook: duplicate delivery ignored', ['tx_ref' => $txRef]);
            return response('Already received', 200);
        }

        // ── 5. Persist raw payload before doing anything else ─────────────────
        $log = WebhookLog::create([
            'source'  => 'flutterwave',
            'event'   => $event,
            'tx_ref'  => $txRef,
            'payload' => $payload,
            'status'  => 'pending',
        ]);

        // ── 6. Queue the actual processing — return 200 instantly ────────────
        ProcessWebhook::dispatch($log->id)->onQueue('webhooks');

        return response('Received', 200);
    }
}
