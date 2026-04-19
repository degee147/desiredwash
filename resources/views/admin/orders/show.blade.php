<x-app-layout>
    <x-slot name="title">Order #{{ substr($order->id, 0, 8) }}…</x-slot>

    <section class="row">
        <div class="col-lg-8">

            {{-- Order header --}}
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        Order <code>{{ substr($order->id, 0, 8) }}…</code>
                    </h4>
                    <div>
                        {!! $statusBadge !!}
                        {!! $paymentBadge !!}
                    </div>
                </div>
                <div class="card-content">

                    <div class="card-body">

                        {{-- Status stepper --}}
                        @php
                            $steps = ['pending', 'confirmed', 'picked_up', 'processing', 'ready', 'delivered'];
                            $currentIndex = array_search($order->status, $steps);
                        @endphp
                        @if ($order->status !== 'cancelled')
                            <div class="d-flex align-items-center mb-2" style="gap:4px;flex-wrap:wrap">
                                @foreach ($steps as $i => $step)
                                    <span
                                        class="badge {{ $i < $currentIndex ? 'badge-success' : ($i === $currentIndex ? 'badge-primary' : 'badge-default') }}">
                                        {{ str_replace('_', ' ', $step) }}
                                    </span>
                                    @if (!$loop->last)
                                        <i class="fa fa-angle-right text-muted" style="font-size:10px"></i>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        {{-- Advance / Cancel actions --}}
                        @php
                            $statusFlow = [
                                'pending' => 'confirmed',
                                'confirmed' => 'picked_up',
                                'picked_up' => 'processing',
                                'processing' => 'ready',
                                'ready' => 'delivered',
                            ];
                            $nextStatus = $statusFlow[$order->status] ?? null;
                            $canCancel = !in_array($order->status, ['delivered', 'cancelled']);
                        @endphp
                        <div class="d-flex" style="gap:8px">
                            @if ($nextStatus)
                                <button type="button" class="btn btn-success btn-raised advance-status-btn"
                                    data-next="{{ $nextStatus }}"
                                    data-url="{{ route('admin.orders.advance-status', $order->id) }}"
                                    data-redirect="{{ route('admin.orders.show', $order->id) }}">
                                    <i class="fa fa-arrow-right"></i>
                                    Mark as {{ str_replace('_', ' ', $nextStatus) }}
                                </button>
                            @endif
                            @if ($canCancel)
                                <button type="button" class="btn btn-danger btn-raised cancel-order-btn"
                                    data-url="{{ route('admin.orders.cancel', $order->id) }}"
                                    data-redirect="{{ route('admin.orders.show', $order->id) }}">
                                    <i class="fa fa-times"></i> Cancel Order
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            {{-- Order items --}}
            <div class="card mb-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">Items</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Unit price</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->items ?? [] as $item)
                                <tr>
                                    <td>{{ $item['emoji'] ?? '' }} {{ $item['service_name'] ?? '—' }}</td>
                                    <td class="text-right">{{ $item['quantity'] ?? 1 }}</td>
                                    <td class="text-right">₦{{ number_format((float) ($item['unit_price'] ?? 0), 2) }}
                                    </td>
                                    <td class="text-right">₦{{ number_format((float) ($item['total'] ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center">No items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Subtotal</th>
                                <th class="text-right">₦{{ number_format((float) $order->subtotal, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right font-weight-normal">Delivery fee</th>
                                <th class="text-right font-weight-normal">
                                    ₦{{ number_format((float) $order->delivery_fee, 2) }}</th>
                            </tr>
                            <tr class="border-top">
                                <th colspan="3" class="text-right">Total</th>
                                <th class="text-right">₦{{ number_format((float) $order->total, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Delivery info --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Delivery details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Zone</dt>
                        <dd class="col-sm-8">{{ $order->zone_name ?? '—' }}</dd>

                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8">{{ $order->address ?? '—' }}</dd>

                        <dt class="col-sm-4">Pickup date</dt>
                        <dd class="col-sm-8">{{ $order->scheduled_pickup_date?->format('M j, Y') ?? '—' }}</dd>

                        <dt class="col-sm-4">Pickup time</dt>
                        <dd class="col-sm-8">{{ $order->scheduled_pickup_time ?? '—' }}</dd>

                        <dt class="col-sm-4">Placed</dt>
                        <dd class="col-sm-8">{{ $order->created_at?->format('M j, Y g:i a') ?? '—' }}</dd>

                        @if ($order->notes)
                            <dt class="col-sm-4">Notes</dt>
                            <dd class="col-sm-8">{{ $order->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Customer --}}
            @include('partials.customer', ['user' => $order->user])
            {{-- Payment --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6">Status</dt>
                        <dd class="col-6">{!! $paymentBadge !!}</dd>

                        <dt class="col-6">Subtotal</dt>
                        <dd class="col-6">₦{{ number_format((float) $order->subtotal, 2) }}</dd>

                        <dt class="col-6">Delivery fee</dt>
                        <dd class="col-6">₦{{ number_format((float) $order->delivery_fee, 2) }}</dd>

                        <dt class="col-6">Total</dt>
                        <dd class="col-6"><strong>₦{{ number_format((float) $order->total, 2) }}</strong></dd>

                        @if ($order->payment_reference)
                            <dt class="col-6">Reference</dt>
                            <dd class="col-6"><code style="font-size:11px">{{ $order->payment_reference }}</code></dd>
                        @endif
                    </dl>
                </div>
            </div>

        </div>
    </section>

    <script>
        jQuery(document).ready(function() {
            $(document).on('click', '.advance-status-btn', function() {
                const btn = $(this);
                const next = btn.data('next');
                if (!confirm(`Mark order as "${next.replace(/_/g,' ')}"?`)) return;
                $.post(btn.data('url'), {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(() => location.href = btn.data('redirect'))
                    .fail(xhr => alert(xhr.responseJSON?.message ?? 'Error'));
            });

            $(document).on('click', '.cancel-order-btn', function() {
                if (!confirm('Cancel this order?')) return;
                const btn = $(this);
                $.post(btn.data('url'), {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(() => location.href = btn.data('redirect'))
                    .fail(xhr => alert(xhr.responseJSON?.message ?? 'Error'));
            });
        });
    </script>
</x-app-layout>
