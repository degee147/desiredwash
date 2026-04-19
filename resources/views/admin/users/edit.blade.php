<x-app-layout>
    <x-slot name="title">Edit User — {{ $user->name }}</x-slot>

    <section class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit user</h4>
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

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Basic info --}}
                        <h6 class="text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:.08em">
                            Basic info
                        </h6>

                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Full name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Phone</label>
                                <input type="text" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone) }}" placeholder="e.g. 09012345678">
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Zone</label>
                                <select name="zone_id" class="form-control @error('zone_id') is-invalid @enderror">
                                    <option value="">— None —</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}"
                                            {{ old('zone_id', $user->zone_id) == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }} ({{ $zone->area }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('zone_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $user->address) }}" placeholder="Delivery address">
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr>

                        {{-- Wallet --}}
                        {{-- <h6 class="text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:.08em">
                            Wallet
                        </h6>

                        <div class="col-sm-5 form-group px-0">
                            <label>Wallet balance (₦)</label>
                            <input type="number" name="wallet_balance" step="0.01" min="0"
                                class="form-control @error('wallet_balance') is-invalid @enderror"
                                value="{{ old('wallet_balance', $user->wallet_balance) }}">
                            <small class="text-muted">Edit with care — this directly adjusts the user's balance.</small>
                            @error('wallet_balance')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        {{-- <hr> --}}

                        {{-- Roles — only superadmin can change roles --}}
                        <h6 class="text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:.08em">
                            Roles
                        </h6>

                        @if (auth()->user()->isSuperAdmin())
                            <div class="d-flex mb-3" style="gap:24px;flex-wrap:wrap">
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="sa" value="0">
                                    <input type="checkbox" class="custom-control-input" id="sa" name="sa"
                                        value="1" {{ old('sa', $user->sa) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="sa">Super admin</label>
                                </div>

                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="admin" value="0">
                                    <input type="checkbox" class="custom-control-input" id="admin" name="admin"
                                        value="1" {{ old('admin', $user->admin) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="admin">Admin</label>
                                </div>

                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="support" value="0">
                                    <input type="checkbox" class="custom-control-input" id="support" name="support"
                                        value="1" {{ old('support', $user->support) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="support">Support</label>
                                </div>
                            </div>
                        @else
                            <p class="text-muted small">Only super admins can change role assignments.</p>
                        @endif

                        <div class="d-flex" style="gap:8px">
                            <button type="submit" class="btn btn-primary btn-raised">
                                <i class="fa fa-save"></i> Save changes
                            </button>
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

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
                </div>
            </div>

            {{-- Account details --}}
            <div class="card mb-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">Account</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6">Wallet</dt>
                        <dd class="col-6">₦{{ number_format((float) $user->wallet_balance, 2) }}</dd>

                        <dt class="col-6">Zone</dt>
                        <dd class="col-6">{{ $user->zone_id ?? '—' }}</dd>

                        <dt class="col-6">Joined</dt>
                        <dd class="col-6">{{ $user->created_at?->format('M j, Y') ?? '—' }}</dd>

                        <dt class="col-6">Updated</dt>
                        <dd class="col-6">{{ $user->updated_at?->format('M j, Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="card mb-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick actions</h5>
                </div>
                <div class="card-body d-flex flex-column" style="gap:8px">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary btn-raised">
                        <i class="fa fa-search"></i> View profile
                    </a>
                    <a href="{{ route('admin.users.resetPassword', $user->id) }}"
                        onclick="return confirm('Reset password for {{ addslashes($user->name) }}?')"
                        class="btn btn-sm btn-secondary btn-raised">
                        <i class="fa fa-key"></i> Reset password
                    </a>

                    {{-- Fund wallet --}}
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

                </div>
            </div>
            {{-- <div class="card mb-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick actions</h5>
                </div>
                <div class="card-body d-flex flex-column" style="gap:8px">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary btn-raised">
                        <i class="fa fa-search"></i> View profile
                    </a>
                    <a href="{{ route('admin.users.resetPassword', $user->id) }}"
                        onclick="return confirm('Reset password for {{ addslashes($user->name) }}?')"
                        class="btn btn-sm btn-secondary btn-raised">
                        <i class="fa fa-key"></i> Reset password
                    </a>
                    <form method="POST" action="{{ route('admin.users.toggleStatus', $user->id) }}">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Toggle status for {{ addslashes($user->name) }}?')"
                            class="btn btn-sm btn-info btn-raised w-100">
                            <i class="fa fa-toggle-on"></i> Toggle status
                        </button>
                    </form>
                </div>
            </div> --}}

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
