<x-app-layout>
    <x-slot name="title">Change Password</x-slot>

    <section id="horizontal-form-layouts">
        <div class="row">
            <div class="col-sm-12">
                <div class="content-header">Change your password</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{-- Optional: Add a card title or info here --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">

                        <form action="{{ route('dashboard.changePassword') }}" method="POST"
                            class="form form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">
                                <div class="form-group row">
                                    <label class="col-md-3 label-control" for="old_password">Current Password:
                                        <span class="required" aria-required="true"> * </span>
                                    </label>
                                    <div class="col-md-6">
                                        <div class="position-relative has-icon-left">
                                            <input type="password" name="old_password" id="old_password"
                                                class="form-control" required value="{{ old('old_password') }}">
                                            <div class="form-control-position">
                                                <i class="fa fa-key"></i>
                                            </div>
                                            @error('old_password')
                                                <span class="invalid-feedback"
                                                    role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 label-control" for="password1">New Password:
                                        <span class="required" aria-required="true"> * </span>
                                    </label>
                                    <div class="col-md-6">
                                        <div class="position-relative has-icon-left">
                                            <input type="password" name="password1" id="password1" class="form-control"
                                                required value="{{ old('password1') }}">
                                            <div class="form-control-position">
                                                <i class="fa fa-key"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 label-control" for="password1_confirmation">Confirm New
                                        Password:
                                        <span class="required" aria-required="true"> * </span>
                                    </label>
                                    <div class="col-md-6">
                                        <div class="position-relative has-icon-left">
                                            <input type="password" name="password1_confirmation"
                                                id="password1_confirmation" class="form-control" required
                                                value="{{ old('password1_confirmation') }}">
                                            <div class="form-control-position">
                                                <i class="fa fa-key"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row clearfix">
                                    <div class="col-sm-9 offset-3">
                                        <button type="submit" class="btn btn-raised btn-primary">
                                            {{ __('Change Password') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
