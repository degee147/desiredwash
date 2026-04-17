{{-- External CSS & JS --}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.css"
    integrity="sha512-9tISBnhZjiw7MV4a1gbemtB9tmPcoJ7ahj8QWIc0daBCdvlKjEA48oLlo6zALYm3037tPYYulT0YQyJIJJoyMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"
    integrity="sha512-F636MAkMAhtTplahL9F6KmTfxTmYcAcjcCkyu0f0voT3N/6vzAuJ4Num55a0gEJ+hRLHhdz3vDvZpf6kqgEa5w=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@include('partials.dashboard_css')
@include('partials.dashboard_custom_css')

<link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/switchery.min.css') }}">

<div class="row">


</div>
@include('partials.daterangepicker_script')
<style>
    /* .card-block { height: 165px; } */
</style>


<script src="{{ asset('js/vendor/switchery.min.js') }}" type="text/javascript"></script>

<script>
    function reloadPage() {
        setTimeout(() => {
            window.location.href = "{{ url()->current() }}";
        }, 2000);
    }


    $(document).ready(function() {
        var listening = true;

    });
</script>
