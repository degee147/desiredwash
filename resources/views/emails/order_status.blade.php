<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Update</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .wrapper { max-width: 560px; margin: 0 auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .header { background: linear-gradient(135deg, #FF6B6B, #FFB347); padding: 32px; text-align: center; }
        .header .emoji { font-size: 48px; display: block; margin-bottom: 10px; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; font-weight: 800; }
        .body { padding: 32px; }
        .body p { color: #555; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
        .order-box { background: #fafafa; border-radius: 12px; padding: 20px 24px; margin: 20px 0; border-left: 4px solid #FF6B6B; }
        .order-box .label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #999; margin-bottom: 4px; }
        .order-box .value { font-size: 14px; color: #1a1a2e; font-weight: 600; margin-bottom: 14px; }
        .order-box .value:last-child { margin-bottom: 0; }
        .items-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .items-table th { text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: #999; padding: 0 0 8px; border-bottom: 1px solid #eee; }
        .items-table td { font-size: 13px; color: #444; padding: 8px 0; border-bottom: 1px solid #f5f5f5; }
        .items-table td:last-child { text-align: right; font-weight: 600; color: #1a1a2e; }
        .total-row td { font-weight: 700; color: #1a1a2e; font-size: 14px; border-bottom: none; padding-top: 12px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; background: #fff3f3; color: #FF6B6B; }
        .badge.express { background: #fff8e6; color: #e6980a; }
        .footer { text-align: center; padding: 20px 32px; color: #aaa; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <span class="emoji">{{ $statusEmoji }}</span>
                <h1>{{ $statusTitle }}</h1>
            </div>
            <div class="body">
                <p>Hi {{ $user->name }},</p>
                <p>{{ $statusMessage }}</p>

                <div class="order-box">
                    <div class="label">Order Reference</div>
                    <div class="value">#{{ strtoupper(substr($order->id, 0, 8)) }}</div>

                    <div class="label">Order Type</div>
                    <div class="value">
                        <span class="badge {{ $order->order_type === 'express' ? 'express' : '' }}">
                            {{ ucfirst($order->order_type ?? 'Standard') }}
                        </span>
                    </div>

                    <div class="label">Pickup Date & Time</div>
                    <div class="value">
                        {{ \Carbon\Carbon::parse($order->scheduled_pickup_date)->format('D, M j, Y') }}
                        at {{ $order->scheduled_pickup_time }}
                    </div>

                    <div class="label">Delivery Address</div>
                    <div class="value">{{ $order->address }}</div>
                </div>

                @php $items = is_array($order->items) ? $order->items : json_decode($order->items, true); @endphp
                @if($items && count($items))
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item['service_name'] ?? $item['emoji'] ?? '' }} {{ $item['service_name'] ?? '' }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>₦{{ number_format($item['total'], 0) }}</td>
                        </tr>
                        @endforeach
                        @if((float)$order->delivery_fee > 0)
                        <tr>
                            <td colspan="2">Delivery fee</td>
                            <td>₦{{ number_format($order->delivery_fee, 0) }}</td>
                        </tr>
                        @endif
                        <tr class="total-row">
                            <td colspan="2">Total</td>
                            <td>₦{{ number_format($order->total, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
                @endif

                <p style="color:#999;font-size:13px;margin-top:24px">If you have questions, reply to this email or contact support via the app.</p>
                <p style="color:#999;font-size:13px">— The DesiredWash Team</p>
            </div>
            <div class="footer">
                © {{ date('Y') }} DesiredWash · All rights reserved
            </div>
        </div>
    </div>
</body>
</html>
