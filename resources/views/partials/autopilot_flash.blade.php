@if (session('primary') || session('message'))
    <div class="alert alert-primary alert-dismissible mb-2" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <!-- <span aria-hidden="true">&times;</span> -->
            <span aria-hidden="true"><i class="fa fa-asterisk" aria-hidden="true"></i></span>
        </button>
        <strong>{!! session('primary') ?? session('message') !!}</strong>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-2" role="alert"
        style="color:black !important; padding-right: 0;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <!-- <span aria-hidden="true">&times;</span> -->
            <span aria-hidden="true"><i class="fa fa-asterisk" aria-hidden="true"></i></span>
        </button>
        <!-- <strong>Success! </strong> -->
        {!! session('success') !!}
    </div>
@endif

{{-- session()->push('info', 'First message');
session()->push('info', 'Second message'); --}}
{{-- @if (session('info'))
    @foreach ((array) session('info') as $msg)
        <div class="alert alert-info alert-dismissible mb-2" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="fa fa-asterisk" aria-hidden="true"></i></span>
            </button>
            <strong>{!! $msg !!}</strong>
        </div>
    @endforeach
@endif --}}
@if (session('info') || session('default'))
    <div class="alert alert-info alert-dismissible mb-2" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <!-- <span aria-hidden="true">&times;</span> -->
            <span aria-hidden="true"><i class="fa fa-asterisk" aria-hidden="true"></i></span>
        </button>
        <strong>{!! session('info') ?? session('default') !!}</strong>
    </div>
@endif

@if (session('failed') || session('error'))
    <div class="alert alert-danger alert-dismissible mb-2" role="alert" style="color:black !important">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <!-- <span aria-hidden="true">&times;</span> -->
            <span aria-hidden="true"><i class="fa fa-asterisk" aria-hidden="true"></i></span>
        </button>
        <!-- <strong>Error! </strong> -->
        {!! session('failed') ?? session('error') !!}
    </div>
@endif
