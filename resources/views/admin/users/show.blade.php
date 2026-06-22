<x-app-layout>
    <x-slot name="title">{{ $user->name }}</x-slot>

    <section class="row">

        {{-- ── LEFT COLUMN ──────────────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Stat tiles --}}
            <div class="row mb-3">
                <div class="col-6 col-md-3 mb-2">
                    <div class="card text-center h-100 py-3">
                        <div style="font-size:22px;font-weight:800;color:#3c4fe0">
                            {{ $orderStats['total'] }}
                        </div>
                        <div class="text-muted small">Total orders</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="card text-center h-100 py-3">
                        <div style="font-size:22px;font-weight:800;color:#28a745">
                            {{ $orderStats['delivered'] }}
                        </div>
                        <div class="text-muted small">Delivered</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="card text-center h-100 py-3">
                        <div style="font-size:22px;font-weight:800;color:#FF6B6B">
                            ₦{{ number_format($orderStats['total_spent'], 0) }}
                        </div>
                        <div class="text-muted small">Total spent</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="card text-center h-100 py-3">
                        <div style="font-size:22px;font-weight:800;color:#e6980a">
                            ₦{{ number_format($walletStats['total_funded'], 0) }}
                        </div>
                        <div class="text-muted small">Total funded</div>
                    </div>
                </div>
            </div>

            {{-- Profile --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Profile</h5>
                    <div class="d-flex align-items-center" style="gap:6px">
                        @if ($user->isSuperAdmin())
                            <span class="badge badge-danger">Super admin</span>
                        @elseif ($user->isAdmin())
                            <span class="badge badge-primary">Admin</span>
                        @elseif ($user->isSupport())
                            <span class="badge badge-info">Support</span>
                        @else
                            <span class="badge badge-secondary">Customer</span>
                        @endif
                        <a href="{{ route('admin.users.edit', $user->id) }}"
                           class="btn btn-sm btn-outline-primary ml-1">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3 text-muted">Full name</dt>
                        <dd class="col-sm-9">{{ $user->name }}</dd>

                        <dt class="col-sm-3 text-muted">Email</dt>
                        <dd class="col-sm-9">
                            {{ $user->email }}
                            @if ($user->auth_provider)
                                <span class="badge badge-light border ml-1">
                                    via {{ $user->auth_provider }}
                                </span>
                            @endif
                        </dd>

                        <dt class="col-sm-3 text-muted">Phone</dt>
                        <dd class="col-sm-9">
                            @if ($user->phone)
                                <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3 text-muted">Address</dt>
                        <dd class="col-sm-9">{{ $user->address ?? '—' }}</dd>

                        <dt class="col-sm-3 text-muted">Zone</dt>
                        <dd class="col-sm-9">
                            @if ($zone)
                                {{ $zone->name }}
                                <span class="text-muted small">({{ $zone->area }})</span>
                                @if (!$zone->is_available)
                                    <span class="badge badge-warning ml-1">Unavailable</span>
                                @endif
                                <br>
                                <span class="text-muted small">
                                    Delivery fee: ₦{{ number_format((float) $zone->delivery_fee, 0) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3 text-muted">Joined</dt>
                        <dd class="col-sm-9">
                            {{ $user->created_at?->format('M j, Y') ?? '—' }}
                            <span class="text-muted small">({{ $user->created_at?->diffForHumans() }})</span>
                        </dd>
                    </dl>
                </div>
            </div>

            {{-- Orders --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Orders</h5>
                    <div class="d-flex align-items-center" style="gap:6px">
                        @if ($orderStats['express'] > 0)
                            <span class="badge badge-warning text-dark">
                                ⚡ {{ $orderStats['express'] }} express
                            </span>
                        @endif
                        @if ($orderStats['cancelled'] > 0)
                            <span class="badge badge-danger">
                                {{ $orderStats['cancelled'] }} cancelled
                            </span>
                        @endif
                        <span class="badge badge-pill badge-primary">
                            {{ $orderStats['total'] }} total
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($user->orders->isEmpty())
                        <p class="text-muted small p-3 mb-0">No orders yet.</p>
                    @else
                        <div class="table-responsive">
                            @include('partials.orders_table', [
                                'userid'    => $user->id,
                                'viewpage'  => true,
                                'searching' => auth()->user()->sa,
                            ])
                        </div>
                    @endif
                </div>
            </div>

            {{-- Wallet transactions --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Wallet Transactions</h5>
                    <div class="d-flex align-items-center" style="gap:6px">
                        @if ($walletStats['pending_topups'] > 0)
                            <span class="badge badge-warning text-dark">
                                {{ $walletStats['pending_topups'] }} pending
                            </span>
                        @endif
                        <span class="badge badge-pill badge-success">
                            {{ $user->walletTransactions->count() }} total
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($user->walletTransactions->isEmpty())
                        <p class="text-muted small p-3 mb-0">No wallet transactions yet.</p>
                    @else
                        <div class="table-responsive">
                            @include('partials.wallet_transactions_table', [
                                'userid'   => $user->id,
                                'viewpage' => true,
                            ])
                        </div>
                    @endif
                </div>
            </div>

            {{-- Transactions --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Payment Transactions</h5>
                    <span class="badge badge-pill badge-secondary">
                        {{ $user->transactions->count() }}
                    </span>
                </div>
                <div class="card-body p-0">
                    @if ($user->transactions->isEmpty())
                        <p class="text-muted small p-3 mb-0">No transactions yet.</p>
                    @else
                        <div class="table-responsive">
                            @include('partials.transactions_table', [
                                'userid'   => $user->id,
                                'viewpage' => true,
                            ])
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── RIGHT SIDEBAR ─────────────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Avatar + identity --}}
            <div class="card mb-3">
                <div class="card-body text-center py-4">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                             class="rounded-circle mb-3"
                             style="width:80px;height:80px;object-fit:cover;border:3px solid #f0f0f0">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width:80px;height:80px;font-size:32px;font-weight:800;
                                    background:linear-gradient(135deg,#FF6B6B,#FFB347);color:#fff">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <p class="mb-0 font-weight-bold" style="font-size:16px">{{ $user->name }}</p>
                    <p class="text-muted small mb-2">{{ $user->email }}</p>
                    <p class="text-muted small mb-0">
                        Member since {{ $user->created_at?->format('M Y') }}
                    </p>
                </div>
            </div>

            {{-- Wallet --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Wallet</h5>
                </div>
                <div class="card-body">
                    <h2 class="mb-0 font-weight-800 text-success">
                        ₦{{ number_format($walletStats['balance'], 2) }}
                    </h2>
                    <p class="text-muted small mb-3">Current balance</p>

                    <dl class="row mb-0" style="font-size:13px">
                        <dt class="col-7 text-muted font-weight-normal">Total funded</dt>
                        <dd class="col-5 text-right mb-1 font-weight-600">
                            ₦{{ number_format($walletStats['total_funded'], 0) }}
                        </dd>
                        <dt class="col-7 text-muted font-weight-normal">Total spent</dt>
                        <dd class="col-5 text-right mb-1 font-weight-600">
                            ₦{{ number_format($walletStats['total_spent'], 0) }}
                        </dd>
                    </dl>

                    @if ($walletStats['pending_topups'] > 0)
                        <div class="alert alert-warning py-1 px-2 mt-2 mb-0 small">
                            {{ $walletStats['pending_topups'] }} pending top-up(s)
                        </div>
                    @endif
                </div>

                {{-- Fund wallet -- superadmin only --}}
                @if (auth()->user()->isSuperAdmin())
                    <div class="card-footer">
                        <form method="POST" action="{{ route('admin.users.fundUser', $user->id) }}">
                            @csrf
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₦</span>
                                </div>
                                <input type="number" name="amount" min="1" step="0.01"
                                       class="form-control" placeholder="Amount to credit" required>
                                <div class="input-group-append">
                                    <button type="submit"
                                        onclick="return confirm('Credit wallet for {{ addslashes($user->name) }}?')"
                                        class="btn btn-sm btn-success">
                                        <i class="fa fa-plus"></i> Credit
                                    </button>
                                </div>
                            </div>
                            @error('amount')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </form>
                    </div>
                @endif
            </div>

            {{-- Virtual account --}}
            @if ($user->va_account_number)
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Virtual Account</h5>
                        <span class="badge badge-success">Active</span>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0" style="font-size:13px">
                            <dt class="col-5 text-muted font-weight-normal">Bank</dt>
                            <dd class="col-7 mb-1">{{ $user->va_bank_name ?? '—' }}</dd>

                            <dt class="col-5 text-muted font-weight-normal">Account no.</dt>
                            <dd class="col-7 mb-1">
                                <strong style="letter-spacing:1px;font-size:15px">
                                    {{ $user->va_account_number }}
                                </strong>
                            </dd>

                            <dt class="col-5 text-muted font-weight-normal">Account name</dt>
                            <dd class="col-7 mb-0">{{ $user->va_account_name ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>
            @else
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Virtual Account</h5>
                        <span class="badge badge-secondary">Not provisioned</span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-0">
                            Will be created automatically when the user opens their wallet.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Order insights --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Insights</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0" style="font-size:13px">
                        <dt class="col-7 text-muted font-weight-normal">Avg. order value</dt>
                        <dd class="col-5 text-right mb-1 font-weight-600">
                            ₦{{ number_format($orderStats['avg_order'], 0) }}
                        </dd>

                        <dt class="col-7 text-muted font-weight-normal">Express orders</dt>
                        <dd class="col-5 text-right mb-1 font-weight-600">
                            {{ $orderStats['express'] }}
                            @if ($orderStats['total'] > 0)
                                <span class="text-muted font-weight-normal">
                                    ({{ round($orderStats['express'] / $orderStats['total'] * 100) }}%)
                                </span>
                            @endif
                        </dd>

                        <dt class="col-7 text-muted font-weight-normal">Pending orders</dt>
                        <dd class="col-5 text-right mb-1 font-weight-600">
                            {{ $orderStats['pending'] }}
                        </dd>

                        <dt class="col-7 text-muted font-weight-normal">Cancellations</dt>
                        <dd class="col-5 text-right mb-1 font-weight-600">
                            {{ $orderStats['cancelled'] }}
                            @if ($orderStats['total'] > 0)
                                <span class="text-muted font-weight-normal">
                                    ({{ round($orderStats['cancelled'] / $orderStats['total'] * 100) }}%)
                                </span>
                            @endif
                        </dd>

                        @if ($orderStats['last_order'])
                            <dt class="col-7 text-muted font-weight-normal">Last order</dt>
                            <dd class="col-5 text-right mb-0 font-weight-600">
                                {{ $orderStats['last_order']->created_at?->diffForHumans() }}
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Recent activity --}}
            @if ($recentActivity->isNotEmpty())
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach ($recentActivity as $item)
                                <li class="list-group-item px-3 py-2" style="font-size:13px">
                                    <div class="d-flex align-items-center">
                                        <span class="text-{{ $item['color'] }} mr-2" style="width:16px;text-align:center">
                                            <i class="fa {{ $item['icon'] }}"></i>
                                        </span>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="font-weight-600 text-truncate">{{ $item['label'] }}</div>
                                            <div class="text-muted" style="font-size:11px">{{ $item['sub'] }}</div>
                                        </div>
                                        <div class="text-muted ml-2 text-nowrap" style="font-size:11px">
                                            {{ $item['time']?->diffForHumans(short: true) }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Quick actions --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body d-flex flex-column" style="gap:8px">
                    <a href="{{ route('admin.users.edit', $user->id) }}"
                       class="btn btn-sm btn-primary btn-raised">
                        <i class="fa fa-pencil"></i> Edit profile
                    </a>
                    <a href="{{ route('admin.users.resetPassword', $user->id) }}"
                       onclick="return confirm('Reset password for {{ addslashes($user->name) }}?')"
                       class="btn btn-sm btn-secondary btn-raised">
                        <i class="fa fa-key"></i> Reset password
                    </a>
                </div>
            </div>

            {{-- Danger zone --}}
            @if (auth()->user()->isSuperAdmin())
                <div class="card border-danger">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-danger">Danger Zone</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">
                            Permanently delete this user and all their data. Cannot be undone.
                        </p>
                        <form action="{{ route('admin.users.destroy', $user->id) }}"
                              method="POST"
                              onsubmit="return confirm('Delete {{ addslashes($user->name) }} permanently? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-raised">
                                <i class="fa fa-trash"></i> Delete user
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </section>

</x-app-layout>
