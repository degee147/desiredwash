<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Order;
use App\Models\Service;
use App\Models\WalletTransaction;
use App\Models\Zone;
use App\Services\AppContextService;
use App\Services\FlutterwaveService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct(
        private FlutterwaveService $flutterwave,
        private NotificationService $notifications,
        private AppContextService $appContextService,
    ) {}

    // POST /api/v1/orders
    public function store(Request $request)
    {
        $data = $request->validate([
            'zone_id'               => 'required|string|exists:zones,id',
            'address'               => 'required|string|max:500',
            'items'                 => 'required|array|min:1',
            'items.*.service_id'    => 'required|integer|exists:services,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'scheduled_pickup_date' => 'required|date|after_or_equal:today',
            'scheduled_pickup_time' => 'required|string',
            'payment_method'        => 'required|in:card,bank_transfer,wallet',
            'order_type'            => 'nullable|in:standard,express',
            'notes'                 => 'nullable|string|max:1000',
        ]);

        $zone = Zone::findOrFail($data['zone_id']);

        if (!$zone->is_available) {
            return response()->json(['message' => 'Selected zone is not currently available'], 422);
        }

        // ── Resolve services from DB ──────────────────────────────────────────
        $serviceIds = collect($data['items'])->pluck('service_id');
        $services   = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        $orderType = $data['order_type'] ?? 'standard';
        $isExpress = $orderType === 'express';

        // Express multiplier comes from backend settings (default 1.8)
        $expressMultiplier = (float) Option::get('express_multiplier', 1.8);

        $items = collect($data['items'])->map(function ($item) use ($services, $isExpress, $expressMultiplier) {
            $service = $services->get($item['service_id']);

            if (!$service) {
                abort(422, 'Invalid service selected');
            }

            $basePrice = (float) $service->price;
            $unitPrice = $isExpress
                ? round($basePrice * $expressMultiplier, 2)
                : $basePrice;

            return [
                'service_id'   => $service->id,
                'service_name' => $service->name,
                'emoji'        => $service->emoji,
                'quantity'     => $item['quantity'],
                'unit_price'   => $unitPrice,
                'total'        => $item['quantity'] * $unitPrice,
            ];
        });

        // ── Calculate totals ──────────────────────────────────────────────────
        $subtotal    = $items->sum('total');
        $deliveryFee = (float) $zone->delivery_fee;
        $total       = $subtotal + $deliveryFee;

        $user = $request->user();

        // ── Wallet payment (instant) ──────────────────────────────────────────
        if ($data['payment_method'] === 'wallet') {

            if ((float) $user->wallet_balance < $total) {
                return response()->json(['message' => 'Insufficient wallet balance'], 402);
            }

            return DB::transaction(function () use ($user, $data, $zone, $items, $subtotal, $deliveryFee, $total, $orderType) {

                $order = Order::create([
                    'user_id'               => $user->id,
                    'zone_id'               => $zone->id,
                    'zone_name'             => $zone->name,
                    'address'               => $data['address'],
                    'items'                 => $items->values(),
                    'scheduled_pickup_date' => $data['scheduled_pickup_date'],
                    'scheduled_pickup_time' => $data['scheduled_pickup_time'],
                    'subtotal'              => $subtotal,
                    'delivery_fee'          => $deliveryFee,
                    'total'                 => $total,
                    'payment_method'        => 'wallet',
                    'payment_status'        => 'success',
                    'status'                => 'confirmed',
                    'order_type'            => $orderType,
                    'notes'                 => $data['notes'] ?? null,
                    'payment_reference'     => Str::uuid(),
                ]);

                WalletTransaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'debit',
                    'amount'      => $total,
                    'status'      => 'completed',
                    'description' => "Payment for order #{$order->id}",
                    'reference'   => $order->id,
                ]);

                $ref = strtoupper(substr($order->id, 0, 8));
                $this->notifications->orderConfirmed($user->id, $order->id, $ref);
                $this->notifications->paymentSuccess($user->id, $total, $ref);

                return response()->json([
                    'order'        => $order->toApiArray(),
                    'payment_link' => null,
                ], 201);
            });
        }

        // ── Card / Bank (pending) ─────────────────────────────────────────────
        $order = Order::create([
            'user_id'               => $user->id,
            'zone_id'               => $zone->id,
            'zone_name'             => $zone->name,
            'address'               => $data['address'],
            'items'                 => $items->values(),
            'scheduled_pickup_date' => $data['scheduled_pickup_date'],
            'scheduled_pickup_time' => $data['scheduled_pickup_time'],
            'subtotal'              => $subtotal,
            'delivery_fee'          => $deliveryFee,
            'total'                 => $total,
            'payment_method'        => $data['payment_method'],
            'payment_status'        => 'pending',
            'status'                => 'pending',
            'order_type'            => $orderType,
            'notes'                 => $data['notes'] ?? null,
            'payment_reference'     => Str::uuid(),
        ]);

        $paymentLink = $this->flutterwave->createOrderPaymentLink([
            'total'      => $total,
            'user_email' => $user->email,
            'user_name'  => $user->name,
        ], $order->id);

        return response()->json([
            'order'        => $order->toApiArray(),
            'payment_link' => $paymentLink,
        ], 201);
    }

    // GET /api/v1/orders
    public function index(Request $request)
    {
        $query = $request->user()->orders()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'data' => collect($orders->items())->map(fn($o) => $o->toApiArray()),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    // GET /api/v1/orders/:id
    public function show(Request $request, string $id)
    {
        $order = $request->user()->orders()->findOrFail($id);
        return response()->json(['order' => $order->toApiArray()]);
    }

    // POST /api/v1/orders/:id/pay
    public function pay(Request $request, string $id)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:wallet,card',
        ]);

        $order = $request->user()->orders()->findOrFail($id);

        if ($order->payment_status === 'success') {
            return response()->json(['message' => 'This order has already been paid'], 422);
        }

        if ($order->status === 'cancelled') {
            return response()->json(['message' => 'Cannot pay for a cancelled order'], 422);
        }

        $user = $request->user();

        if ($data['payment_method'] === 'wallet') {

            if ((float) $user->wallet_balance < (float) $order->total) {
                return response()->json(['message' => 'Insufficient wallet balance'], 402);
            }

            return DB::transaction(function () use ($user, $order) {

                $order->update([
                    'payment_method' => 'wallet',
                    'payment_status' => 'success',
                    'status'         => 'confirmed',
                ]);

                WalletTransaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'debit',
                    'amount'      => $order->total,
                    'status'      => 'completed',
                    'description' => "Payment for order #{$order->id}",
                    'reference'   => $order->id,
                ]);

                $this->appContextService->updateUserBalance($user->id);

                $ref = strtoupper(substr($order->id, 0, 8));
                $this->notifications->orderConfirmed($user->id, $order->id, $ref);
                $this->notifications->paymentSuccess($user->id, $order->total, $ref);

                return response()->json([
                    'order'        => $order->fresh()->toApiArray(),
                    'payment_link' => null,
                ]);
            });
        }

        // Card — generate fresh Flutterwave link
        $order->update(['payment_method' => 'card']);

        $paymentLink = $this->flutterwave->createOrderPaymentLink([
            'total'      => $order->total,
            'user_email' => $user->email,
            'user_name'  => $user->name,
        ], $order->id);

        return response()->json([
            'order'        => $order->fresh()->toApiArray(),
            'payment_link' => $paymentLink,
        ]);
    }

    // POST /api/v1/orders/:id/cancel
    public function cancel(Request $request, string $id)
    {
        $order = $request->user()->orders()->findOrFail($id);

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Order cannot be cancelled at this stage'], 422);
        }

        DB::transaction(function () use ($order, $request) {
            $order->update(['status' => 'cancelled']);

            if ($order->payment_method === 'wallet' && $order->payment_status === 'success') {
                $user = $request->user();

                WalletTransaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'credit',
                    'amount'      => $order->total,
                    'status'      => 'completed',
                    'description' => "Refund for cancelled order #{$order->id}",
                    'reference'   => $order->id,
                ]);

                $this->appContextService->updateUserBalance($user->id);
            }
        });

        $wasWalletRefund = $order->payment_method === 'wallet' && $order->payment_status === 'success';
        $ref             = strtoupper(substr($order->id, 0, 8));
        $this->notifications->orderCancelled($request->user()->id, $ref, $wasWalletRefund);

        return response()->json(['order' => $order->fresh()->toApiArray()]);
    }
}
