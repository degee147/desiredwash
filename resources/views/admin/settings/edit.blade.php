<x-app-layout>
    <x-slot name="title">Settings</x-slot>

    <section class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Settings</h4>
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

                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf

                        {{-- Contact --}}
                        <h6 class="text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:.08em">
                            Contact
                        </h6>

                        <div class="form-group">
                            <label>Phone number <span class="text-danger">*</span></label>
                            <input type="text" name="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $phone) }}" placeholder="e.g. 09053083000" required>
                            <small class="text-muted">Shown to customers on the app and website.</small>
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Pricing --}}
                        <h6 class="text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:.08em">
                            Pricing
                        </h6>

                        <div class="form-group">
                            <label>Express Order Multiplier <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.1" min="1" max="10" name="express_multiplier"
                                    class="form-control @error('express_multiplier') is-invalid @enderror"
                                    value="{{ old('express_multiplier', $express_multiplier) }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">×</span>
                                </div>
                            </div>
                            <small class="text-muted">
                                Express price = base price × this value.
                                Default is <strong>1.8</strong>.
                                Example: a ₦500 service becomes ₦{{ number_format(500 * ($express_multiplier ?: 1.8), 0) }} for express.
                            </small>
                            @error('express_multiplier')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Order Type Copy --}}
                        <h6 class="text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:.08em">
                            Order Type Labels
                        </h6>
                        <p class="text-muted" style="font-size:13px;margin-top:-4px">
                            Text shown on the mobile app's order type selector and order summary.
                            Use <code>{multiplier}</code> as a placeholder — it's automatically replaced
                            with the Express Order Multiplier above (e.g. <code>1.8</code>).
                        </p>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Standard order — title <span class="text-danger">*</span></label>
                                <input type="text" name="standard_order_label"
                                    class="form-control @error('standard_order_label') is-invalid @enderror"
                                    value="{{ old('standard_order_label', $standard_order_label) }}" required>
                                @error('standard_order_label')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Standard order — subtitle <span class="text-danger">*</span></label>
                                <input type="text" name="standard_order_subtitle"
                                    class="form-control @error('standard_order_subtitle') is-invalid @enderror"
                                    value="{{ old('standard_order_subtitle', $standard_order_subtitle) }}" required>
                                @error('standard_order_subtitle')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Express order — title <span class="text-danger">*</span></label>
                                <input type="text" name="express_order_label"
                                    class="form-control @error('express_order_label') is-invalid @enderror"
                                    value="{{ old('express_order_label', $express_order_label) }}" required>
                                @error('express_order_label')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Express order — subtitle <span class="text-danger">*</span></label>
                                <input type="text" name="express_order_subtitle"
                                    class="form-control @error('express_order_subtitle') is-invalid @enderror"
                                    value="{{ old('express_order_subtitle', $express_order_subtitle) }}" required>
                                @error('express_order_subtitle')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Express order — badge text <span class="text-danger">*</span></label>
                            <input type="text" name="express_order_badge"
                                class="form-control @error('express_order_badge') is-invalid @enderror"
                                value="{{ old('express_order_badge', $express_order_badge) }}" required>
                            <small class="text-muted">
                                Small badge on the Express card. Preview:
                                <strong>{{ str_replace('{multiplier}', $express_multiplier ?: 1.8, old('express_order_badge', $express_order_badge)) }}</strong>
                            </small>
                            @error('express_order_badge')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Express order — summary banner text <span class="text-danger">*</span></label>
                            <input type="text" name="express_order_summary_label"
                                class="form-control @error('express_order_summary_label') is-invalid @enderror"
                                value="{{ old('express_order_summary_label', $express_order_summary_label) }}" required>
                            <small class="text-muted">
                                Shown in the order summary card when Express is selected. Preview:
                                <strong>{{ str_replace('{multiplier}', $express_multiplier ?: 1.8, old('express_order_summary_label', $express_order_summary_label)) }}</strong>
                            </small>
                            @error('express_order_summary_label')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex" style="gap:8px">
                            <button type="submit" class="btn btn-primary btn-raised">
                                <i class="fa fa-save"></i> Save settings
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">About</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Total options</dt>
                        <dd class="col-7">{{ \App\Models\Option::count() }}</dd>

                        <dt class="col-5">Last updated</dt>
                        <dd class="col-7">
                            {{ \App\Models\Option::latest('updated_at')->first()?->updated_at?->format('M j, Y g:i a') ?? '—' }}
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Express Pricing Preview</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2" style="font-size:13px">
                        At ×{{ $express_multiplier ?? 1.8 }} multiplier:
                    </p>
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>Standard</th><th>Express</th></tr>
                        </thead>
                        <tbody>
                            @foreach([500, 1000, 2000, 3500, 5000] as $price)
                            <tr>
                                <td>₦{{ number_format($price, 0) }}</td>
                                <td class="text-warning font-weight-bold">₦{{ number_format($price * ($express_multiplier ?: 1.8), 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Mobile App Preview</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em">
                        Order type selector
                    </p>
                    <div class="d-flex" style="gap:8px;margin-bottom:16px">
                        <div style="flex:1;border:1px solid #eee;border-radius:10px;padding:10px;font-size:13px">
                            <strong>{{ $standard_order_label }}</strong><br>
                            <span class="text-muted" style="font-size:12px">{{ $standard_order_subtitle }}</span>
                        </div>
                        <div style="flex:1;border:1px solid #ffd080;border-radius:10px;padding:10px;font-size:13px;background:#fff8e6">
                            <strong>{{ $express_order_label }}</strong><br>
                            <span class="text-muted" style="font-size:12px">{{ $express_order_subtitle }}</span><br>
                            <span class="badge badge-warning text-dark mt-1">
                                {{ str_replace('{multiplier}', $express_multiplier ?: 1.8, $express_order_badge) }}
                            </span>
                        </div>
                    </div>
                    <p class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em">
                        Order summary banner (express selected)
                    </p>
                    <div style="font-size:12px;color:#e6980a;font-weight:700">
                        ⚡ {{ str_replace('{multiplier}', $express_multiplier ?: 1.8, $express_order_summary_label) }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
