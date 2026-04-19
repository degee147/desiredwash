<x-app-layout>
    <x-slot name="title">Transaction {{ substr($transaction->tx_ref, 0, 12) }}…</x-slot>

    <section class="row">
        <div class="col-lg-8">

            {{-- Header --}}
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        Transaction <code>{{ substr($transaction->tx_ref, 0, 12) }}…</code>
                    </h4>
                    <div class="d-flex" style="gap:6px">
                        @php
                            $statusColor = match ($transaction->status) {
                                'successful' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'cancelled' => 'secondary',
                                default => 'secondary',
                            };
                            $typeColor = match ($transaction->type) {
                                'order_payment' => 'primary',
                                'wallet_topup' => 'info',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge badge-{{ $typeColor }}">
                            {{ str_replace('_', ' ', $transaction->type) }}
                        </span>
                        <span class="badge badge-{{ $statusColor }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <dl class="row mb-0">
                                <dt class="col-6">Amount</dt>
                                <dd class="col-6">
                                    <strong>₦{{ number_format((float) $transaction->amount, 2) }}</strong>
                                </dd>

                                <dt class="col-6">Currency</dt>
                                <dd class="col-6">{{ $transaction->currency ?? 'NGN' }}</dd>

                                <dt class="col-6">Type</dt>
                                <dd class="col-6">{{ str_replace('_', ' ', $transaction->type) }}</dd>

                                <dt class="col-6">Status</dt>
                                <dd class="col-6">
                                    <span class="badge badge-{{ $statusColor }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-sm-6">
                            <dl class="row mb-0">
                                <dt class="col-6">Date</dt>
                                <dd class="col-6">{{ $transaction->created_at?->format('M j, Y g:i a') ?? '—' }}</dd>

                                <dt class="col-6">Updated</dt>
                                <dd class="col-6">{{ $transaction->updated_at?->format('M j, Y g:i a') ?? '—' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- References --}}
            <div class="card mb-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">References</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">TX ref</dt>
                        <dd class="col-sm-8"><code>{{ $transaction->tx_ref ?? '—' }}</code></dd>

                        <dt class="col-sm-4">Flutterwave TX ID</dt>
                        <dd class="col-sm-8"><code>{{ $transaction->flw_tx_id ?? '—' }}</code></dd>

                        <dt class="col-sm-4">Flutterwave ref</dt>
                        <dd class="col-sm-8"><code>{{ $transaction->flw_ref ?? '—' }}</code></dd>
                    </dl>
                </div>
            </div>

            {{-- Meta --}}
            @if (!empty($transaction->meta))
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Raw metadata</h5>
                    </div>
                    <div class="card-body p-0">
                        <pre class="mb-0 p-2" style="font-size:12px;max-height:340px;overflow:auto;background:transparent">{{ json_encode($transaction->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Customer --}}
            @include('partials.customer', ['user' => $transaction->user])

            {{-- Linked order --}}
            @if ($transaction->order)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Linked order</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-2">
                            <dt class="col-6">Order ID</dt>
                            <dd class="col-6">
                                <code>{{ substr($transaction->order->id, 0, 8) }}…</code>
                            </dd>

                            <dt class="col-6">Status</dt>
                            <dd class="col-6">
                                <span class="badge badge-secondary">
                                    {{ ucfirst($transaction->order->status) }}
                                </span>
                            </dd>

                            <dt class="col-6">Total</dt>
                            <dd class="col-6">₦{{ number_format((float) $transaction->order->total, 2) }}</dd>
                        </dl>
                        <a href="{{ route('admin.orders.show', $transaction->order->id) }}"
                            class="btn btn-sm btn-primary btn-raised">
                            <i class="fa fa-search"></i> View order
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </section>
</x-app-layout>
