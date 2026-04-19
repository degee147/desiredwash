<x-app-layout>
    <x-slot name="title">Settings</x-slot>

    <section class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Settings</h4>
                </div>
                <div class="card-body">

                    {{-- @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif --}}

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
        </div>
    </section>
</x-app-layout>
