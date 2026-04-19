<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\TraitsManager;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use TraitsManager;
    public function index()
    {
        return view('admin.orders.index');
    }

    public function show(Order $order)
    {
        $order->load('user');

        // dd($order->toArray());

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

        $order->update($data);

        return back()->with('success', 'Order status updated.');
    }

    public function advanceStatus(Request $request, Order $order)
    {
        $next = self::STATUS_FLOW[$order->status] ?? null;

        abort_if(!$next, 422, 'Order cannot be advanced from this status.');

        $order->update(['status' => $next]);

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

        return response()->json(['status' => 'cancelled', 'badge' => $this->badge('cancelled')]);
    }


}
