<x-app-layout>
    <x-slot name="title">Edit Price — {{ $price->item_name }}</x-slot>

    <section class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit price</h4>
                    <span class="badge badge-{{ $price->is_active ? 'success' : 'secondary' }}">
                        {{ $price->is_active ? 'Active' : 'Inactive' }}
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

                    <form action="{{ route('admin.prices.update', $price->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Item name <span class="text-danger">*</span></label>
                                <input type="text" name="item_name"
                                    class="form-control @error('item_name') is-invalid @enderror"
                                    value="{{ old('item_name', $price->item_name) }}" required>
                                @error('item_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Icon class</label>
                                <input type="text" name="icon_class"
                                    class="form-control @error('icon_class') is-invalid @enderror"
                                    value="{{ old('icon_class', $price->icon_class) }}" placeholder="e.g. fa fa-tshirt">
                                @error('icon_class')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <input type="text" name="category"
                                    class="form-control @error('category') is-invalid @enderror"
                                    value="{{ old('category', $price->category) }}" required>
                                @error('category')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Service type <span class="text-danger">*</span></label>
                                <input type="text" name="service_type"
                                    class="form-control @error('service_type') is-invalid @enderror"
                                    value="{{ old('service_type', $price->service_type) }}" required>
                                @error('service_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Regular price (₦) <span class="text-danger">*</span></label>
                                <input type="number" name="regular_price" step="0.01" min="0"
                                    class="form-control @error('regular_price') is-invalid @enderror"
                                    value="{{ old('regular_price', $price->regular_price) }}" required>
                                @error('regular_price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-6 form-group">
                                <label>Express price (₦)</label>
                                <input type="number" name="express_price" step="0.01" min="0"
                                    class="form-control @error('express_price') is-invalid @enderror"
                                    value="{{ old('express_price', $price->express_price) }}"
                                    placeholder="Leave blank if not applicable">
                                @error('express_price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Optional short description">{{ old('description', $price->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group d-flex align-items-center" style="gap:10px">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $price->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                            <small class="text-muted">Inactive prices are hidden from customers.</small>
                        </div>

                        <div class="d-flex" style="gap:8px">
                            <button type="submit" class="btn btn-primary btn-raised">
                                <i class="fa fa-save"></i> Save changes
                            </button>
                            <a href="{{ route('admin.prices.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Current values</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6">Regular</dt>
                        <dd class="col-6">₦{{ $price->formatted_regular_price }}</dd>

                        <dt class="col-6">Express</dt>
                        <dd class="col-6">
                            {{ $price->formatted_express_price ? '₦' . $price->formatted_express_price : '—' }}</dd>

                        <dt class="col-6">Category</dt>
                        <dd class="col-6">{{ $price->category }}</dd>

                        <dt class="col-6">Service type</dt>
                        <dd class="col-6">{{ $price->service_type }}</dd>

                        <dt class="col-6">Created</dt>
                        <dd class="col-6">{{ $price->created_at?->format('M j, Y') ?? '—' }}</dd>

                        <dt class="col-6">Updated</dt>
                        <dd class="col-6">{{ $price->updated_at?->format('M j, Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            @if (auth()->user()->isSuperAdmin())
                <div class="card mt-2 border-danger">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-danger">Danger zone</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">Permanently delete this price. This cannot be undone.</p>
                        <form action="{{ route('admin.prices.destroy', $price->id) }}" method="POST"
                            onsubmit="return confirm('Delete this price permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-raised">
                                <i class="fa fa-trash"></i> Delete price
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </section>
</x-app-layout>
