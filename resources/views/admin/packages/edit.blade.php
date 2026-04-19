<x-app-layout>
    <x-slot name="title">Edit Package — {{ $package->name }}</x-slot>

    <section class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit package</h4>
                    <div class="d-flex" style="gap:6px">
                        @if ($package->is_featured)
                            <span class="badge badge-warning">Featured</span>
                        @endif
                        <span class="badge badge-{{ $package->is_active ? 'success' : 'secondary' }}">
                            {{ $package->is_active ? 'Active' : 'Inactive' }}
                        </span>
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

                    <form action="{{ route('admin.packages.update', $package->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-sm-8 form-group">
                                <label>Package name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $package->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-4 form-group">
                                <label>Icon class</label>
                                <input type="text" name="icon_class"
                                    class="form-control @error('icon_class') is-invalid @enderror"
                                    value="{{ old('icon_class', $package->icon_class) }}" placeholder="e.g. fa fa-box">
                                @error('icon_class')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Subtitle</label>
                            <input type="text" name="subtitle"
                                class="form-control @error('subtitle') is-invalid @enderror"
                                value="{{ old('subtitle', $package->subtitle) }}"
                                placeholder="Short tagline shown below the package name">
                            @error('subtitle')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Price (₦) <span class="text-danger">*</span></label>
                                <input type="number" name="price" step="0.01" min="0"
                                    class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price', $package->price) }}" required>
                                @error('price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Old price (₦)
                                    <small class="text-muted">— shown as strikethrough</small>
                                </label>
                                <input type="number" name="old_price" step="0.01" min="0"
                                    class="form-control @error('old_price') is-invalid @enderror"
                                    value="{{ old('old_price', $package->old_price) }}"
                                    placeholder="Leave blank if no discount">
                                @error('old_price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Items --}}
                        <div class="form-group">
                            <label>Items
                                <small class="text-muted">— one per line</small>
                            </label>
                            <textarea name="items_raw" rows="5" class="form-control @error('items_raw') is-invalid @enderror"
                                placeholder="e.g.&#10;5 shirts&#10;3 trousers&#10;Free pickup">{{ old('items_raw', implode("\n", $package->items ?? [])) }}</textarea>
                            <small class="text-muted">Each line becomes a bullet point on the package card.</small>
                            @error('items_raw')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-sm-4 form-group px-0">
                            <label>Sort order</label>
                            <input type="number" name="sort_order" min="0"
                                class="form-control @error('sort_order') is-invalid @enderror"
                                value="{{ old('sort_order', $package->sort_order ?? 0) }}">
                            <small class="text-muted">Lower = appears first.</small>
                            @error('sort_order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex mb-3" style="gap:24px">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>

                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured"
                                    value="1" {{ old('is_featured', $package->is_featured) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_featured">Featured</label>
                            </div>
                        </div>

                        <div class="d-flex" style="gap:8px">
                            <button type="submit" class="btn btn-primary btn-raised">
                                <i class="fa fa-save"></i> Save changes
                            </button>
                            <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
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
                        <dt class="col-5">Price</dt>
                        <dd class="col-7">₦{{ $package->formatted_price }}</dd>

                        @if ($package->has_discount)
                            <dt class="col-5">Old price</dt>
                            <dd class="col-7">
                                <s class="text-muted">₦{{ $package->formatted_old_price }}</s>
                                <span class="badge badge-danger ml-1">-{{ $package->discount_percentage }}%</span>
                            </dd>
                        @endif

                        <dt class="col-5">Items</dt>
                        <dd class="col-7">{{ count($package->items ?? []) }} item(s)</dd>

                        <dt class="col-5">Sort order</dt>
                        <dd class="col-7">{{ $package->sort_order }}</dd>

                        <dt class="col-5">Created</dt>
                        <dd class="col-7">{{ $package->created_at?->format('M j, Y') ?? '—' }}</dd>

                        <dt class="col-5">Updated</dt>
                        <dd class="col-7">{{ $package->updated_at?->format('M j, Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            @if (!empty($package->items))
                <div class="card mb-2">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Current items</h5>
                    </div>
                    <div class="card-body py-2">
                        <ul class="mb-0 pl-3">
                            @foreach ($package->items as $item)
                                <li class="small">{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if (auth()->user()->isSuperAdmin())
                <div class="card border-danger">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-danger">Danger zone</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">Permanently delete this package. This cannot be undone.</p>
                        <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST"
                            onsubmit="return confirm('Delete this package permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-raised">
                                <i class="fa fa-trash"></i> Delete package
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </section>
</x-app-layout>
