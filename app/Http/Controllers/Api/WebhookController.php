<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWebhook;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    // POST /api/v1/webhooks/flutterwave
    public function handle(Request $request)
    {
        // ── 1. Validate signature (fast, before anything else) ────────────────
        $secret = config('services.flutterwave.webhook_secret');
        if ($secret && $request->header('verif-hash') !== $secret) {
            Log::warning('Flutterwave webhook: invalid hash');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? null;
        $txData = $payload['data'] ?? [];
        $txRef = $txData['tx_ref'] ?? null;

        Log::info('Flutterwave webhook received', ['event' => $event, 'tx_ref' => $txRef]);

        // ── 2. Only care about successful charges ─────────────────────────────
        if ($event !== 'charge.completed' || ($txData['status'] ?? null) !== 'successful') {
            return response()->json(['message' => 'Event ignored']);
        }

        // ── 3. Duplicate detection — same tx_ref already queued/processed? ────
        $duplicate = WebhookLog::where('tx_ref', $txRef)
            ->whereIn('status', ['pending', 'processing', 'processed'])
            ->exists();

        if ($duplicate) {
            Log::info('Flutterwave webhook: duplicate delivery ignored', ['tx_ref' => $txRef]);
            return response()->json(['message' => 'Already received']);
        }

        // ── 4. Persist raw payload immediately ────────────────────────────────
        $log = WebhookLog::create([
            'source' => 'flutterwave',
            'event' => $event,
            'tx_ref' => $txRef,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        // ── 5. Dispatch to queue — controller is done, 200 returns instantly ──
        ProcessWebhook::dispatch($log->id)->onQueue('webhooks');

        return response()->json(['message' => 'Received'], 200);
    }
}
