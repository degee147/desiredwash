<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    ) {
    }

    // POST /api/v1/orders
    public function store(Request $request)
    {
        $data = $request->validate([
            'zone_id' => 'required|string|exists:zones,id',
            'address' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|integer|exists:services,id',
            'items.*.quantity' => 'required|integer|min:1',
            'scheduled_pickup_date' => 'required|date|after_or_equal:today',
            'scheduled_pickup_time' => 'required|string',
            'payment_method' => 'required|in:card,bank_transfer,wallet',
            'notes' => 'nullable|string|max:1000',
        ]);

        $zone = Zone::findOrFail($data['zone_id']);

        if (!$zone->is_available) {
            return response()->json(['message' => 'Selected zone is not currently available'], 422);
        }

        // =========================
        // RESOLVE SERVICES FROM DB
        // =========================
        $serviceIds = collect($data['items'])->pluck('service_id');

        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        $items = collect($data['items'])->map(function ($item) use ($services) {
            $service = $services->get($item['service_id']);

            if (!$service) {
                abort(422, 'Invalid service selected');
            }

            return [
                'service_id' => $service->id,
                'service_name' => $service->name,
                'emoji' => $service->emoji,
                'quantity' => $item['quantity'],
                'unit_price' => (float) $service->price,
                'total' => $item['quantity'] * (float) $service->price,
            ];
        });

        // =========================
        // CALCULATE TOTALS
        // =========================
        $subtotal = $items->sum('total');
        $deliveryFee = (float) $zone->delivery_fee;
        $total = $subtotal + $deliveryFee;

        $user = $request->user();

        // =========================
        // WALLET PAYMENT (INSTANT)
        // =========================
        if ($data['payment_method'] === 'wallet') {

            if ((float) $user->wallet_balance < $total) {
                return response()->json(['message' => 'Insufficient wallet balance'], 402);
            }


            return DB::transaction(function () use ($user, $data, $zone, $items, $subtotal, $deliveryFee, $total) {

                $order = Order::create([
                    'user_id' => $user->id,
                    'zone_id' => $zone->id,
                    'zone_name' => $zone->name,
                    'address' => $data['address'],
                    'items' => $items->values(),
                    'scheduled_pickup_date' => $data['scheduled_pickup_date'],
                    'scheduled_pickup_time' => $data['scheduled_pickup_time'],
                    'subtotal' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'total' => $total,
                    'payment_method' => 'wallet',
                    'payment_status' => 'success',
                    'status' => 'confirmed',
                    'notes' => $data['notes'] ?? null,
                    'payment_reference' => \Illuminate\Support\Str::uuid(),
                ]);

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'amount' => $total,
                    'status' => "completed",
                    'description' => "Payment for order #{$order->id}",
                    'reference' => $order->id,
                ]);

                // 🔔 Notify: order confirmed + payment success (wallet)
                $ref = strtoupper(substr($order->id, 0, 8));
                $this->notifications->orderConfirmed($user->id, $order->id, $ref);
                $this->notifications->paymentSuccess($user->id, $total, $ref);

                return response()->json([
                    'order' => $order->toApiArray(),
                    'payment_link' => null,
                ], 201);
            });


        }

        // =========================
        // CARD / BANK (PENDING)
        // =========================
        $order = Order::create([
            'user_id' => $user->id,
            'zone_id' => $zone->id,
            'zone_name' => $zone->name,
            'address' => $data['address'],
            'items' => $items->values(),
            'scheduled_pickup_date' => $data['scheduled_pickup_date'],
            'scheduled_pickup_time' => $data['scheduled_pickup_time'],
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'payment_method' => $data['payment_method'],
            'payment_status' => 'pending',
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
            'payment_reference' => Str::uuid(),
        ]);

        $paymentLink = $this->flutterwave->createOrderPaymentLink([
            'total' => $total,
            'user_email' => $user->email,
            'user_name' => $user->name,
        ], $order->id);

        return response()->json([
            'order' => $order->toApiArray(),
            'payment_link' => $paymentLink,
        ], 201);
    }


    // GET /api/v1/orders
    public function index(Request $request)
    {
        $query = $request->user()->orders()->latest();

        // Optional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Pagination (default 10 per page)
        $orders = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'data' => collect($orders->items())->map(fn($o) => $o->toApiArray()),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
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

        // Only allow paying for orders that are still awaiting payment
        if ($order->payment_status === 'success') {
            return response()->json(['message' => 'This order has already been paid'], 422);
        }

        if ($order->status === 'cancelled') {
            return response()->json(['message' => 'Cannot pay for a cancelled order'], 422);
        }

        $user = $request->user();

        // ==========================================
        // WALLET PAYMENT
        // ==========================================
        if ($data['payment_method'] === 'wallet') {

            if ((float) $user->wallet_balance < (float) $order->total) {
                return response()->json(['message' => 'Insufficient wallet balance'], 402);
            }

            return DB::transaction(function () use ($user, $order) {

                $order->update([
                    'payment_method' => 'wallet',
                    'payment_status' => 'success',
                    'status' => 'confirmed',
                ]);

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'amount' => $order->total,
                    'status' => 'completed',
                    'description' => "Payment for order #{$order->id}",
                    'reference' => $order->id,
                ]);

                $this->appContextService->updateUserBalance($user->id);

                // 🔔 Notify: order confirmed + payment success
                $ref = strtoupper(substr($order->id, 0, 8));
                $this->notifications->orderConfirmed($user->id, $order->id, $ref);
                $this->notifications->paymentSuccess($user->id, $order->total, $ref);

                return response()->json([
                    'order' => $order->fresh()->toApiArray(),
                    'payment_link' => null,
                ]);
            });
        }

        // ==========================================
        // CARD PAYMENT — generate a fresh Flutterwave link
        // ==========================================
        $order->update(['payment_method' => 'card']);

        $paymentLink = $this->flutterwave->createOrderPaymentLink([
            'total' => $order->total,
            'user_email' => $user->email,
            'user_name' => $user->name,
        ], $order->id);

        return response()->json([
            'order' => $order->fresh()->toApiArray(),
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
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $order->total,
                    'status' => "completed",
                    'description' => "Refund for cancelled order #{$order->id}",
                    'reference' => $order->id,
                ]);

                $this->appContextService->updateUserBalance($user->id);
            }
        });

        // 🔔 Notify: order cancelled
        $wasWalletRefund = $order->payment_method === 'wallet' && $order->payment_status === 'success';
        $ref = strtoupper(substr($order->id, 0, 8));
        $this->notifications->orderCancelled($request->user()->id, $ref, $wasWalletRefund);

        return response()->json(['order' => $order->fresh()->toApiArray()]);
    }
}
