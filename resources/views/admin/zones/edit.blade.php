<x-app-layout>
    <x-slot name="title">Edit Zone — {{ $zone->name }}</x-slot>

    <section class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit zone</h4>
                    <span class="badge badge-{{ $zone->is_available ? 'success' : 'secondary' }}">
                        {{ $zone->is_available ? 'Available' : 'Unavailable' }}
                    </span>
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

                    <form action="{{ route('admin.zones.update', $zone->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>Zone ID <span class="text-danger">*</span></label>
                                <input type="text" name="id"
                                    class="form-control @error('id') is-invalid @enderror"
                                    value="{{ old('id', $zone->id) }}" required>
                                <small class="text-muted">Short unique identifier e.g. <code>z01</code></small>
                                @error('id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-8 form-group">
                                <label>Zone name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $zone->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Area <span class="text-danger">*</span></label>
                            <input type="text" name="area"
                                class="form-control @error('area') is-invalid @enderror"
                                value="{{ old('area', $zone->area) }}" placeholder="e.g. GRA Phase 1, Benin City"
                                required>
                            @error('area')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-5 form-group px-0">
                            <label>Delivery fee (₦) <span class="text-danger">*</span></label>
                            <input type="number" name="delivery_fee" step="0.01" min="0"
                                class="form-control @error('delivery_fee') is-invalid @enderror"
                                value="{{ old('delivery_fee', $zone->delivery_fee) }}" required>
                            @error('delivery_fee')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_available" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_available"
                                    name="is_available" value="1"
                                    {{ old('is_available', $zone->is_available) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_available">
                                    Available for delivery
                                </label>
                            </div>
                            <small class="text-muted">Unavailable zones are hidden from customers at checkout.</small>
                        </div>

                        <div class="d-flex" style="gap:8px">
                            <button type="submit" class="btn btn-primary btn-raised">
                                <i class="fa fa-save"></i> Save changes
                            </button>
                            <a href="{{ route('admin.zones.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">Current values</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">ID</dt>
                        <dd class="col-7"><code>{{ $zone->id }}</code></dd>

                        <dt class="col-5">Name</dt>
                        <dd class="col-7">{{ $zone->name }}</dd>

                        <dt class="col-5">Area</dt>
                        <dd class="col-7">{{ $zone->area }}</dd>

                        <dt class="col-5">Delivery fee</dt>
                        <dd class="col-7">₦{{ number_format((float) $zone->delivery_fee, 2) }}</dd>

                        <dt class="col-5">Status</dt>
                        <dd class="col-7">
                            <span class="badge badge-{{ $zone->is_available ? 'success' : 'secondary' }}">
                                {{ $zone->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </dd>

                        <dt class="col-5">Created</dt>
                        <dd class="col-7">{{ $zone->created_at?->format('M j, Y') ?? '—' }}</dd>

                        <dt class="col-5">Updated</dt>
                        <dd class="col-7">{{ $zone->updated_at?->format('M j, Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            @if (auth()->user()->isSuperAdmin())
                <div class="card border-danger">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-danger">Danger zone</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">Permanently delete this zone. This cannot be undone.</p>
                        <form action="{{ route('admin.zones.destroy', $zone->id) }}" method="POST"
                            onsubmit="return confirm('Delete zone {{ $zone->name }} permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-raised">
                                <i class="fa fa-trash"></i> Delete zone
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </section>
</x-app-layout>
