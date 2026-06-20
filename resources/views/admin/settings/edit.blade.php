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
        </div>
    </section>
</x-app-layout>
