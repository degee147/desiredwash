<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- ===================== ROW 1: Stat Cards ===================== --}}
    <div class="row">

        {{-- Total Revenue --}}
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card card-inverse bg-success">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <h3 class="card-text">₦{{ number_format($stats['total_revenue'], 0) }}</h3>
                                <span>Total Revenue</span>
                            </div>
                            <div class="media-right align-self-center">
                                <i class="fa fa-money font-large-2 float-right"></i>
                            </div>
                        </div>
                        <small class="mt-1 d-block" style="opacity:.8">
                            <i class="fa fa-check-circle"></i>
                            {{ $stats['paid_orders_count'] }} paid orders
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Orders --}}
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card card-inverse bg-primary">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <h3 class="card-text">{{ number_format($stats['total_orders']) }}</h3>
                                <span>Total Orders</span>
                            </div>
                            <div class="media-right align-self-center">
                                <i class="fa fa-shopping-basket font-large-2 float-right"></i>
                            </div>
                        </div>
                        <small class="mt-1 d-block" style="opacity:.8">
                            <i class="fa fa-clock-o"></i>
                            {{ $stats['pending_orders_count'] }} pending
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Customers --}}
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card card-inverse bg-info">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <h3 class="card-text">{{ number_format($stats['total_users']) }}</h3>
                                <span>Total Customers</span>
                            </div>
                            <div class="media-right align-self-center">
                                <i class="fa fa-users font-large-2 float-right"></i>
                            </div>
                        </div>
                        <small class="mt-1 d-block" style="opacity:.8">
                            <i class="fa fa-user-plus"></i>
                            {{ $stats['new_users_today'] }} joined today
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wallet in Circulation --}}
        <div class="col-xl-3 col-lg-6 col-12">
            <div class="card card-inverse bg-warning">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body text-left">
                                <h3 class="card-text">₦{{ number_format($stats['total_wallet_balance'], 0) }}</h3>
                                <span>Wallet in Circulation</span>
                            </div>
                            <div class="media-right align-self-center">
                                <i class="fa fa-exchange font-large-2 float-right"></i>
                            </div>
                        </div>
                        <small class="mt-1 d-block" style="opacity:.8">
                            <i class="fa fa-refresh"></i>
                            {{ $stats['wallet_tx_today'] }} transactions today
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- ===================== END ROW 1 ===================== --}}

    {{-- ===================== ROW 2: Order Status Breakdown + Recent Orders ===================== --}}
    <div class="row match-height">

        {{-- Order Status Breakdown --}}
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Order Status</h4>
                    <small class="text-muted">All time breakdown</small>
                </div>
                <div class="card-content">
                    <div class="card-body">

                        @php
                            $statusColors = [
                                'pending' => ['bg-warning', 'fa-clock-o'],
                                'confirmed' => ['bg-info', 'fa-check'],
                                'processing' => ['bg-primary', 'fa-cogs'],
                                'ready' => ['bg-cyan', 'fa-box'],
                                'delivered' => ['bg-success', 'fa-truck'],
                                'cancelled' => ['bg-danger', 'fa-times'],
                            ];
                        @endphp

                        @foreach ($stats['orders_by_status'] as $status => $count)
                            @php
                                [$colorClass, $icon] = $statusColors[$status] ?? ['bg-secondary', 'fa-circle'];
                                $pct = $stats['total_orders'] > 0 ? round(($count / $stats['total_orders']) * 100) : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-capitalize">
                                        <i class="fa {{ $icon }} mr-1 text-muted"></i>
                                        {{ $status }}
                                    </span>
                                    <span class="font-weight-bold">{{ $count }} <span
                                            class="text-muted font-small-2">({{ $pct }}%)</span></span>
                                </div>
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar {{ $colorClass }}" style="width:{{ $pct }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Recent Orders</h4>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary btn-raised">
                        View all <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-content">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Zone</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}">
                                                <code>{{ strtoupper(substr($order->id, 0, 8)) }}</code>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="font-weight-500">{{ $order->user?->name ?? '—' }}</span>
                                            <br><small class="text-muted">{{ $order->user?->phone ?? '' }}</small>
                                        </td>
                                        <td><small>{{ $order->zone_name }}</small></td>
                                        <td class="font-weight-bold">₦{{ number_format((float) $order->total, 0) }}
                                        </td>
                                        <td>
                                            @if ($order->payment_status === 'success')
                                                <span class="badge badge-success">Paid</span>
                                            @else
                                                <span
                                                    class="badge badge-warning">{{ ucfirst($order->payment_status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = match ($order->status) {
                                                    'confirmed' => 'badge-info',
                                                    'processing' => 'badge-primary',
                                                    'delivered' => 'badge-success',
                                                    'cancelled' => 'badge-danger',
                                                    default => 'badge-warning',
                                                };
                                            @endphp
                                            <span
                                                class="badge {{ $statusBadge }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td><small
                                                class="text-muted">{{ $order->created_at?->format('M j, g:i A') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">No orders yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- ===================== END ROW 2 ===================== --}}

    {{-- ===================== ROW 3: Today's Summary + Recent Wallet Transactions + Top Customers ===================== --}}
    <div class="row match-height">

        {{-- Today at a glance --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Today at a Glance</h4>
                    <small class="text-muted">{{ now()->format('l, M j Y') }}</small>
                </div>
                <div class="card-content">
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                <span><i class="fa fa-shopping-basket text-primary mr-2"></i> Orders today</span>
                                <span class="badge badge-pill badge-primary">{{ $stats['orders_today'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                <span><i class="fa fa-money text-success mr-2"></i> Revenue today</span>
                                <span
                                    class="font-weight-bold text-success">₦{{ number_format($stats['revenue_today'], 0) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                <span><i class="fa fa-user-plus text-info mr-2"></i> New users</span>
                                <span class="badge badge-pill badge-info">{{ $stats['new_users_today'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                <span><i class="fa fa-times-circle text-danger mr-2"></i> Cancellations</span>
                                <span class="badge badge-pill badge-danger">{{ $stats['cancelled_today'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                <span><i class="fa fa-exchange text-warning mr-2"></i> Wallet credits</span>
                                <span
                                    class="font-weight-bold text-warning">₦{{ number_format($stats['wallet_credited_today'], 0) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Wallet Transactions --}}
        <div class="col-xl-5 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Wallet Transactions</h4>
                    {{-- <a href="{{ route('admin.wallet_transactions.index') }}"
                        class="btn btn-sm btn-outline-success btn-raised">
                        View all <i class="fa fa-arrow-right ml-1"></i>
                    </a> --}}
                </div>
                <div class="card-content">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th class="text-right">Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentWalletTx as $tx)
                                    <tr>
                                        <td>
                                            <span class="font-weight-500">{{ $tx->user?->name ?? '—' }}</span>
                                            <br><small
                                                class="text-muted">{{ Str::limit($tx->description, 30) }}</small>
                                        </td>
                                        <td>
                                            @if ($tx->type === 'credit')
                                                <span class="badge badge-success">Credit</span>
                                            @else
                                                <span class="badge badge-danger">Debit</span>
                                            @endif
                                        </td>
                                        <td
                                            class="text-right font-weight-bold {{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->type === 'credit' ? '+' : '-' }}₦{{ number_format((float) $tx->amount, 0) }}
                                        </td>
                                        <td><small
                                                class="text-muted">{{ $tx->created_at?->format('M j, g:i A') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No transactions yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Customers by Order Value --}}
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Top Customers</h4>
                    <small class="text-muted">By total order value</small>
                </div>
                <div class="card-content">
                    <div class="card-body pt-1 pb-0">
                        @forelse ($topCustomers as $customer)
                            <div class="media py-2 border-bottom align-items-center">
                                <div class="media-left mr-3">
                                    @if ($customer->avatar_url)
                                        <img src="{{ $customer->avatar_url }}" class="rounded-circle" width="38"
                                            height="38" style="object-fit:cover" alt="{{ $customer->name }}">
                                    @else
                                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center"
                                            style="width:38px;height:38px;font-size:15px;color:#fff;font-weight:600">
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="media-body">
                                    <a href="{{ route('admin.users.show', $customer->id) }}"
                                        class="font-weight-bold d-block mb-0" style="font-size:13px">
                                        {{ $customer->name }}
                                    </a>
                                    <small class="text-muted">{{ $customer->orders_count }} orders</small>
                                </div>
                                <div class="media-right text-right">
                                    <span class="font-weight-bold text-success" style="font-size:13px">
                                        ₦{{ number_format($customer->orders_sum_total, 0) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small py-3 mb-0">No data yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- ===================== END ROW 3 ===================== --}}

    {{-- ===================== ROW 4: Zone Activity + Recent Users ===================== --}}
    <div class="row match-height">

        {{-- Zone Activity --}}
        <div class="col-xl-5 col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Zone Activity</h4>
                    <small class="text-muted">Orders per delivery zone</small>
                </div>
                <div class="card-content">
                    <div class="card-body pt-1">
                        @forelse ($zoneActivity as $zone)
                            @php
                                $pct =
                                    $stats['total_orders'] > 0
                                        ? round(($zone->orders_count / $stats['total_orders']) * 100)
                                        : 0;
                            @endphp
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span style="font-size:12px">
                                        {{ $zone->name }}
                                        @if (!$zone->is_available)
                                            <span class="badge badge-warning ml-1"
                                                style="font-size:9px">Unavailable</span>
                                        @endif
                                    </span>
                                    <span style="font-size:12px" class="text-muted">{{ $zone->orders_count }}
                                        orders</span>
                                </div>
                                <div class="progress" style="height:5px">
                                    <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">No zone data yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Users --}}
        <div class="col-xl-7 col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Recent Customers</h4>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-info btn-raised">
                        View all <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-content">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Zone</th>
                                    <th class="text-right">Wallet</th>
                                    <th>Joined</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentUsers as $u)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($u->avatar_url)
                                                    <img src="{{ $u->avatar_url }}" class="rounded-circle mr-2"
                                                        width="28" height="28" style="object-fit:cover">
                                                @else
                                                    <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mr-2"
                                                        style="width:28px;height:28px;font-size:12px;color:#fff;font-weight:600;flex-shrink:0">
                                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <span style="font-size:13px"
                                                        class="font-weight-500">{{ $u->name }}</span>
                                                    <br><small class="text-muted">{{ $u->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><small>{{ $u->zone->name ?? '—' }}</small></td>
                                        <td class="text-right font-weight-bold text-success">
                                            ₦{{ number_format((float) $u->wallet_balance, 0) }}
                                        </td>
                                        <td><small class="text-muted">{{ $u->created_at?->format('M j') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $u->id) }}"
                                                class="btn btn-xs btn-primary btn-raised btn-icon">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No users yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- ===================== END ROW 4 ===================== --}}

</x-app-layout>
