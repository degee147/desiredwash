<div class="card mb-2">
    <div class="card-header">
        <h5 class="card-title mb-0">Customer</h5>
    </div>

    <div class="card-body">
        <h4 class="card-title">{{ $user?->name ?? '—' }}</h4>
        <p class="card-text">{{ $user?->email ?? '' }}</p>
        <p class="card-text">{{ $user?->phone ?? '' }}</p>
        <a class="btn btn-primary" target="_blank" href="{{ route('admin.users.show', $user->id) }}">View User</a>
    </div>
    {{-- <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->user?->name ?? '—' }}</strong></p>
                    <p class="mb-1 text-muted small">{{ $order->user?->email ?? '' }}</p>
                    <p class="mb-1 text-muted small">{{ $order->user?->phone ?? '' }}</p>
                    @if ($order->user)
                        <a href="{{ route('admin.users.show', $order->user->id) }}"
                            class="btn btn-xs btn-outline-secondary mt-1">
                            View User
                        </a>
                    @endif
                </div> --}}
</div>
