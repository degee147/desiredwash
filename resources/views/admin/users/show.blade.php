<x-app-layout>
    <x-slot name="title">User — {{ $user->name }}</x-slot>

    <section class="row">

        {{-- LEFT: main content --}}
        <div class="col-lg-8">

            {{-- Profile card --}}
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Profile</h4>
                    <div class="d-flex" style="gap:6px">
                        @if ($user->isSuperAdmin())
                            <span class="badge badge-danger">Super admin</span>
                        @elseif ($user->isAdmin())
                            <span class="badge badge-primary">Admin</span>
                        @elseif ($user->isSupport())
                            <span class="badge badge-info">Support</span>
                        @else
                            <span class="badge badge-secondary">Customer</span>
                        @endif
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
                                <span class="badge badge-light border ml-1">via {{ $user->auth_provider }}</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3 text-muted">Phone</dt>
                        <dd class="col-sm-9">{{ $user->phone ?? '—' }}</dd>

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
                                    <i class="fa fa-motorcycle"></i>
                                    Delivery fee: ₦{{ number_format((float) $zone->delivery_fee, 2) }}
                                </span>
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-sm-3 text-muted">Joined</dt>
                        <dd class="col-sm-9">{{ $user->created_at?->format('M j, Y') ?? '—' }}</dd>

                        <dt class="col-sm-3 text-muted">Last updated</dt>
                        <dd class="col-sm-9">{{ $user->updated_at?->format('M j, Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Orders --}}
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Orders</h5>
                    <span class="badge badge-pill badge-primary">{{ $user->orders->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if ($user->orders->isEmpty())
                        <p class="text-muted small p-3 mb-0">No orders yet.</p>
                    @else
                        <div class="table-responsive">
                            @include('partials.orders_table', [
                                'userid' => $user->id,
                                'viewpage' => true,
                                'searching' => auth()->user()->sa,
                            ])
                        </div>
                        @if ($user->orders->count() > 10)
                            <p class="text-muted small p-2 mb-0 text-right">
                                Showing 10 of {{ $user->orders->count() }} orders.
                            </p>
                        @endif
                    @endif
                </div>
            </div>
            {{-- Transactions --}}
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Transactions</h5>
                    <span class="badge badge-pill badge-success">{{ $user->transactions->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if ($user->transactions->isEmpty())
                        <p class="text-muted small p-3 mb-0">No transactions yet.</p>
                    @else
                        <div class="table-responsive">
                            @include('partials.transactions_table', [
                                'userid' => $user->id,
                                'viewpage' => true,
                            ])
                        </div>
                        @if ($user->transactions->count() > 15)
                            <p class="text-muted small p-2 mb-0 text-right">
                                Showing 15 of {{ $user->transactions->count() }} transactions.
                            </p>
                        @endif
                    @endif
                </div>
            </div>
            {{-- Wallet transactions --}}
            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Wallet transactions</h5>
                    <span class="badge badge-pill badge-success">{{ $user->walletTransactions->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if ($user->walletTransactions->isEmpty())
                        <p class="text-muted small p-3 mb-0">No wallet transactions yet.</p>
                    @else
                        <div class="table-responsive">
                            @include('partials.wallet_transactions_table', [
                                'userid' => $user->id,
                                'viewpage' => true,
                            ])
                        </div>
                        @if ($user->walletTransactions->count() > 15)
                            <p class="text-muted small p-2 mb-0 text-right">
                                Showing 15 of {{ $user->walletTransactions->count() }} transactions.
                            </p>
                        @endif
                    @endif
                </div>
            </div>



        </div>

        {{-- RIGHT: sidebar --}}
        <div class="col-lg-4">

            {{-- Avatar + summary --}}
            <div class="card mb-2">
                <div class="card-body text-center">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle mb-2"
                            style="width:72px;height:72px;object-fit:cover">
                    @else
                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-2"
                            style="width:72px;height:72px;font-size:28px;color:#fff">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <p class="mb-0 font-weight-bold">{{ $user->name }}</p>
                    <p class="text-muted small mb-1">{{ $user->email }}</p>
                    @if ($user->auth_provider)
                        <span class="badge badge-light border">via {{ $user->auth_provider }}</span>
                    @endif
                    <div class="mt-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
            {{-- Wallet balance --}}
            <div class="card mb-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">Wallet</h5>
                </div>
                <div class="card-body">
                    <h3 class="mb-0 font-weight-bold text-success">
                        ₦{{ number_format((float) $user->wallet_balance, 2) }}
                    </h3>
                    <p class="text-muted small mb-0">Current balance</p>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="card mb-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick actions</h5>
                </div>
                <div class="card-body d-flex flex-column" style="gap:8px">

                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary btn-raised">
                        <i class="fa fa-pencil"></i> Edit profile
                    </a>

                    <a href="{{ route('admin.users.resetPassword', $user->id) }}"
                        onclick="return confirm('Reset password for {{ addslashes($user->name) }}?')"
                        class="btn btn-sm btn-secondary btn-raised">
                        <i class="fa fa-key"></i> Reset password
                    </a>

                    {{-- Fund wallet — superadmin only --}}
                    @if (auth()->user()->isSuperAdmin())
                        <form method="POST" action="{{ route('admin.users.fundUser', $user->id) }}">
                            @csrf
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₦</span>
                                </div>
                                <input type="number" name="amount" min="1" step="0.01" class="form-control"
                                    placeholder="Amount" required>
                                <div class="input-group-append">
                                    <button type="submit"
                                        onclick="return confirm('Fund wallet for {{ addslashes($user->name) }}?')"
                                        class="btn btn-sm btn-success btn-raised">
                                        <i class="fa fa-plus"></i> Fund
                                    </button>
                                </div>
                            </div>
                            @error('amount')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </form>
                    @endif

                </div>
            </div>

            {{-- Danger zone — superadmin only --}}
            @if (auth()->user()->isSuperAdmin())
                <div class="card border-danger">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-danger">Danger zone</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">Permanently delete this user. This cannot be undone.</p>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                            onsubmit="return confirm('Delete {{ addslashes($user->name) }} permanently?')">
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
