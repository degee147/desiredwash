<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWebhook;
use App\Models\WebhookLog;

class WebhookLogController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $logs = WebhookLog::latest()->paginate(50);

        $counts = [
            'processed'  => WebhookLog::where('status', 'processed')->count(),
            'pending'    => WebhookLog::whereIn('status', ['pending', 'processing'])->count(),
            'failed'     => WebhookLog::where('status', 'failed')->count(),
        ];

        return view('admin.webhooks.index', compact('logs', 'counts'));
    }

    public function retry(WebhookLog $webhookLog)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $webhookLog->update(['status' => 'pending', 'error' => null]);
        ProcessWebhook::dispatch($webhookLog->id)->onQueue('webhooks');

        return back()->with('success', "Webhook #{$webhookLog->id} queued for retry.");
    }
}
