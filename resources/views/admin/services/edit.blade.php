<x-app-layout>
    <x-slot name="title">Edit Service — {{ $service->name }}</x-slot>

    <section class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit service</h4>
                    <span style="font-size:24px">{{ $service->emoji }}</span>
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

                    <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-sm-8 form-group">
                                <label>Service name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $service->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-4 form-group">
                                <label>Emoji</label>
                                <input type="text" name="emoji"
                                    class="form-control @error('emoji') is-invalid @enderror"
                                    value="{{ old('emoji', $service->emoji) }}" placeholder="e.g. 🧺"
                                    style="font-size:20px">
                                @error('emoji')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Price (₦) <span class="text-danger">*</span></label>
                            <input type="number" name="price" step="0.01" min="0"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price', $service->price) }}" required>
                            @error('price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Optional short description">{{ old('description', $service->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex" style="gap:8px">
                            <button type="submit" class="btn btn-primary btn-raised">
                                <i class="fa fa-save"></i> Save changes
                            </button>
                            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card mb-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">Current values</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Name</dt>
                        <dd class="col-7">{{ $service->name }}</dd>

                        <dt class="col-5">Emoji</dt>
                        <dd class="col-7" style="font-size:20px">{{ $service->emoji ?? '—' }}</dd>

                        <dt class="col-5">Price</dt>
                        <dd class="col-7">₦{{ number_format((float) $service->price, 2) }}</dd>

                        <dt class="col-5">Created</dt>
                        <dd class="col-7">{{ $service->created_at?->format('M j, Y') ?? '—' }}</dd>

                        <dt class="col-5">Updated</dt>
                        <dd class="col-7">{{ $service->updated_at?->format('M j, Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            @if (auth()->user()->isSuperAdmin())
                <div class="card border-danger">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-danger">Danger zone</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">Permanently delete this service. This cannot be undone.</p>
                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST"
                            onsubmit="return confirm('Delete this service permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-raised">
                                <i class="fa fa-trash"></i> Delete service
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </section>
</x-app-layout>
