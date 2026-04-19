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
                <label class="col-md-3 label-control" for="phone">Phone:</label>
                <div class="col-md-6">
                    <div class="position-relative has-icon-left">
                        <input type="text" name="phone" id="phone" class="form-control"
                            placeholder="enter phone" autocomplete="off" value="{{ old('phone', $user->phone ?? '') }}">
                        <div class="form-control-position">
                            <i class="fa fa-phone"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-3 label-control" for="address">Address:</label>
                <div class="col-md-6">
                    <div class="position-relative has-icon-left">
                        <textarea name="address" id="address" class="form-control" placeholder="enter address" autocomplete="off">{{ old('address', $user->address ?? '') }}</textarea>
                        <div class="form-control-position">
                            <i class="fa fa-info"></i>
                        </div>
                    </div>
                </div>
            </div>

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
