<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WalletTransaction;
use App\Services\AppContextService;
use App\Services\NotificationService;
use App\Traits\TraitsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use TraitsManager;

    public function __construct(
        private NotificationService $notifications,
        private AppContextService $appContextService,
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
            'orderTypeBadge' => $this->badge($order->order_type ?? 'standard', 'order_type'),
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

        if (!$next) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Order cannot be advanced from this status.'], 422);
            }
            return back()->with('error', 'Order cannot be advanced from this status.');
        }

        $previousStatus = $order->status;
        $order->update(['status' => $next]);

        $this->notifyStatusChange($order, $next, $previousStatus);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => $next,
                'badge' => $this->badge($next),
                'canAdvance' => isset(self::STATUS_FLOW[$next]),
                'nextLabel' => self::STATUS_FLOW[$next] ?? null,
            ]);
        }

        return back()->with('success', 'Order status updated.');
    }

    public function cancelOrder(Request $request, Order $order)
    {
        if (in_array($order->status, ['delivered', 'cancelled'])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Cannot cancel this order.'], 422);
            }
            return back()->with('error', 'Cannot cancel this order.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            if ($order->payment_method === 'wallet' && $order->payment_status === 'success') {
                WalletTransaction::create([
                    'user_id' => $order->user_id,
                    'type' => 'credit',
                    'amount' => $order->total,
                    'status' => 'completed',
                    'description' => "Refund for cancelled order #{$order->id}",
                    'reference' => $order->id,
                ]);

                $this->appContextService->updateUserBalance($order->user_id);
            }
        });

        $wasWalletRefund = $order->payment_method === 'wallet' && $order->payment_status === 'success';
        $ref = strtoupper(substr($order->id, 0, 8));
        $this->notifications->orderCancelled($order->user_id, $ref, $wasWalletRefund);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'cancelled', 'badge' => $this->badge('cancelled')]);
        }

        return back()->with('success', 'Order cancelled.');
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
