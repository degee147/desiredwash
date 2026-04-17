<div class="px-3">
    <form action="{{ $action }}" method="POST" class="form form-horizontal" enctype="multipart/form-data">
        @csrf
        @if (isset($user))
            @method('PUT')
        @endif

        <div class="form-body">
            {{-- <h4 class="form-section"><i class="ft-user"></i> Personal Info</h4> --}}

            @if (!isset($show_fullname) || $show_fullname)
                <div class="form-group row">
                    <label class="col-md-3 label-control" for="name">Full Name:
                        <span class="required" aria-required="true"> * </span>
                    </label>
                    <div class="col-md-6">
                        <div class="position-relative has-icon-left">
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="enter text" autocomplete="off" required
                                value="{{ old('name', $user->name ?? '') }}">
                            <div class="form-control-position">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (!isset($show_othernames) || $show_othernames)
                <div class="form-group row">
                    <label class="col-md-3 label-control" for="name"> Name:</label>
                    <div class="col-md-6">
                        <div class="position-relative has-icon-left">
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="enter text" autocomplete="off" required
                                value="{{ old('name', $user->name ?? '') }}">
                            <div class="form-control-position">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{--
            <div class="form-group row">
                <label class="col-md-3 label-control" for="email"> Email:
                    <span class="required" aria-required="true"> * </span>
                </label>
                <div class="col-md-6">
                    <div class="position-relative has-icon-left">
                        <input type="email"
                               name="email"
                               id="email"
                               class="form-control"
                               placeholder="enter text"
                               autocomplete="off"
                               required
                               value="{{ old('email', $user->email ?? '') }}">
                        <div class="form-control-position">
                            <i class="fa fa-envelope"></i>
                        </div>
                    </div>
                </div>
            </div>
            --}}

            <div class="form-group row">
                <label class="col-md-3 label-control" for="bio">Bio:</label>
                <div class="col-md-6">
                    <div class="position-relative has-icon-left">
                        <textarea name="bio" id="bio" class="form-control" placeholder="enter bio" autocomplete="off">{{ old('bio', $user->bio ?? '') }}</textarea>
                        <div class="form-control-position">
                            <i class="fa fa-info"></i>
                        </div>
                    </div>
                </div>
            </div>

            @if (!isset($show_balance) || $show_balance)
                <div class="form-group row">
                    <label class="col-md-3 label-control" for="opening_live">Opening Live Balance:</label>
                    <div class="col-md-6">
                        <div class="position-relative has-icon-left">
                            <input type="number" name="opening_live" id="opening_live" class="form-control"
                                placeholder="enter phone number" autocomplete="off" step="0.00000001"
                                value="{{ old('opening_live', $user->opening_live ?? '') }}">
                            <div class="form-control-position">
                                <i class="fa fa-bank"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 label-control" for="opening_test">Opening Test Balance:</label>
                    <div class="col-md-6">
                        <div class="position-relative has-icon-left">
                            <input type="number" name="opening_test" id="opening_test" class="form-control"
                                placeholder="enter phone number" autocomplete="off"
                                value="{{ old('opening_test', $user->opening_test ?? '') }}">
                            <div class="form-control-position">
                                <i class="fa fa-bank"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 label-control" for="sa" style="margin-top: 20px;">Super Admin</label>
                    <div class="col-md-6">
                        <input type="checkbox" name="sa" id="sa" class="option-input radio"
                            {{ old('sa', $user->sa ?? false) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 label-control" for="admin" style="margin-top: 20px;"> Admin</label>
                    <div class="col-md-6">
                        <input type="checkbox" name="admin" id="admin" class="option-input radio"
                            {{ old('admin', $user->admin ?? false) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 label-control" for="elite" style="margin-top: 20px;"> Elite</label>
                    <div class="col-md-6">
                        <input type="checkbox" name="elite" id="elite" class="option-input radio"
                            {{ old('elite', $user->elite ?? false) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 label-control" for="oddacity" style="margin-top: 20px;"> Oddacity</label>
                    <div class="col-md-6">
                        <input type="checkbox" name="oddacity" id="oddacity" class="option-input radio"
                            {{ old('oddacity', $user->oddacity ?? false) ? 'checked' : '' }}>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 label-control" for="autopilot" style="margin-top: 20px;">
                        Autopilot</label>
                    <div class="col-md-6">
                        <input type="checkbox" name="autopilot" id="autopilot" class="option-input radio"
                            {{ old('autopilot', $user->autopilot ?? false) ? 'checked' : '' }}>
                    </div>
                </div>
            @endif

            @if (!isset($show_password) || $show_password)
                <div class="form-group row">
                    <label class="col-md-3 label-control" for="description">Password:
                        <span class="required" aria-required="true"> * </span>
                    </label>
                    <div class="col-md-6">
                        <div class="position-relative has-icon-left">
                            <p>The default password is
                                <strong>abcdef</strong>
                            </p>
                            <p>User will be required to change their password</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="form-actions">
            <div class="row clearfix">
                <div class="col-sm-9 offset-3">
                    <button type="submit" class="btn btn-raised btn-primary">
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function() {

        if ($('input[name=active2]').is(':checked')) {
            $('input[name=active]').val(1);
        } else {
            $('input[name=active]').val(0);
        }

        $('#active').change(function() {
            if ($(this).is(':checked')) {
                $('input[name=active]').val(1);
            } else {
                $('input[name=active]').val(0);
            }
        });
        $('#featured2').change(function() {
            if ($(this).is(':checked')) {
                $('input[name=featured]').val(1);
            } else {
                $('input[name=featured]').val(0);
            }
        });
    });
</script>
