<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use App\Traits\TraitsManager;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use TraitsManager;

    public function __construct(
        private NotificationService $notifications,
    ) {
    }

    public function index()
    {
        return view('admin.orders.index');
    }

    public function show(Order $order)
    {
        $order->load('user');

        return view('admin.orders.show', [
            'order' => $order,
            'currentUser' => auth()->user(),
            'statusBadge' => $this->badge($order->status),
            'paymentBadge' => $this->badge($order->payment_status, 'payment'),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,picked_up,processing,ready,delivered,cancelled',
        ]);

        $previousStatus = $order->status;
        $order->update($data);

        $this->notifyStatusChange($order, $data['status'], $previousStatus);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => $data['status'],
                'badge' => $this->badge($data['status']),
                'message' => 'Order status updated.',
            ]);
        }

        return back()->with('success', 'Order status updated.');
    }

    public function advanceStatus(Request $request, Order $order)
    {
        $next = self::STATUS_FLOW[$order->status] ?? null;

        abort_if(!$next, 422, 'Order cannot be advanced from this status.');

        $previousStatus = $order->status;
        $order->update(['status' => $next]);

        $this->notifyStatusChange($order, $next, $previousStatus);

        return response()->json([
            'status' => $next,
            'badge' => $this->badge($next),
            'canAdvance' => isset(self::STATUS_FLOW[$next]),
            'nextLabel' => self::STATUS_FLOW[$next] ?? null,
        ]);
    }

    public function cancelOrder(Request $request, Order $order)
    {
        abort_if(in_array($order->status, ['delivered', 'cancelled']), 422, 'Cannot cancel.');

        $order->update(['status' => 'cancelled']);

        $wasWalletRefund = $order->payment_method === 'wallet' && $order->payment_status === 'success';
        $ref = strtoupper(substr($order->id, 0, 8));
        $this->notifications->orderCancelled($order->user_id, $ref, $wasWalletRefund);

        return response()->json(['status' => 'cancelled', 'badge' => $this->badge('cancelled')]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function notifyStatusChange(Order $order, string $newStatus, string $previousStatus): void
    {
        // Don't fire if status didn't actually change
        if ($newStatus === $previousStatus)
            return;

        $ref = strtoupper(substr($order->id, 0, 8));

        match ($newStatus) {
            'confirmed' => $this->notifications->orderConfirmed($order->user_id, $order->id, $ref),
            'picked_up' => $this->notifications->orderPickedUp($order->user_id, $order->id, $ref),
            'ready' => $this->notifications->orderReady($order->user_id, $order->id, $ref),
            'delivered' => $this->notifications->orderDelivered($order->user_id, $order->id, $ref),
            'cancelled' => $this->notifications->orderCancelled(
                $order->user_id,
                $ref,
                $order->payment_method === 'wallet' && $order->payment_status === 'success'
            ),
            default => null, // pending, processing — no push needed
        };
    }
}
