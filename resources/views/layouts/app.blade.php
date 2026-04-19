@php
    $siteDescription = 'Desired Wash - On-Demand Laundry Service';
@endphp
<!DOCTYPE html>
<html lang="en" class="loading">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="author" content="Cybernek Solutions Limited">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ !empty($title) ? $title . ' :: ' : '' }} {{ $siteDescription }}
    </title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('logo.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link
        href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900"
        rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <!-- END VENDOR CSS-->
    <!-- BEGIN APEX CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/feather/style.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/simple-line-icons/style.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/all.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/dropzone.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/app2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('compressed/css.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/metronicapp/app.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

    <link href="{{ asset('plugins/bootstrap-fileinput/css/fileinput.min.css') }}" media="all" rel="stylesheet"
        type="text/css" />

    <!-- END APEX CSS-->
    <!-- BEGIN Page Level CSS-->
    <!-- END Page Level CSS-->

    {{-- AssetCompress JS equivalent --}}


    <script src="{{ asset('compressed/js1.js') }}" type="text/javascript"></script>
    <script src="{{ asset('compressed/js2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/bootstrap-fileinput/js/plugins/purify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/bootstrap-fileinput/js/fileinput.min.js') }}" type="text/javascript"></script>

    <link rel="stylesheet" type="text/css"
        href="{{ asset('plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/jquery-confirm/dist/jquery-confirm.min.css') }}">
    <script src="{{ asset('plugins/jquery-confirm/dist/jquery-confirm.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.css') }}" />
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/moment/moment@develop/min/moment-with-locales.min.js"></script>
    <link rel="stylesheet" type="text/css"
        href="{{ asset('plugins/material-datetimepicker-gh/css/bootstrap-material-datetimepicker-bs4.css') }}">
    <script src="{{ asset('plugins/material-datetimepicker-gh/js/bootstrap-material-datetimepicker-bs4.js') }}"
        type="text/javascript"></script>

    @include('partials.view_in_modal_plugin')
    @include('partials.custom_scripts')
    <!-- BEGIN Custom CSS-->
    @include('partials.custom_css')
    <!-- END Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/components.css') }}">

</head>

<body data-col="2-columns" class=" 2-columns ">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MH4W5KQ" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="wrapper nav-collapsed menu-collapsed">
        <!-- main menu-->
        @include('partials.app_sidebar', ['page' => $page ?? null])
        <!-- / main menu-->

        <!-- Navbar (Header) Starts-->
        @include('partials.navbar_header')
        <!-- Navbar (Header) Ends-->

        <div class="main-panel">
            <div class="main-content">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-12">
                            @include('partials.autopilot_flash')
                            @include('flash::message')
                        </div>
                    </div>
                    {{ $slot }}
                </div>
            </div>
            <footer class="footer footer-static footer-light">
                <p class="clearfix text-muted text-sm-center px-2">
                    <span>Copyright &copy;
                        {{ date('Y') }}
                        <a href="{{ url('/') }}" target="_blank"
                            class="text-bold-800 primary darken-2">DesiredWash</a>, All rights reserved. Powered by <a
                            href="http://cybernek.com" target="_blank">Cybernek Solutions Limited</a> </span>
                </p>
            </footer>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <!-- START Notification Sidebar-->
    @include('partials.notification_sidebar')
    <!-- END Notification Sidebar-->
    <!-- Theme customizer Starts-->
    {{-- @include('partials.autopilot.theme_customiser') --}}
    <!-- Theme customizer Ends-->
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN APEX JS-->
    <!-- END APEX JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    {{-- <script src="{{ asset('assets/js/dashboard1.js') }}" type="text/javascript"></script> --}}
    <!-- END PAGE LEVEL JS-->
    <!-- Global site tag (gtag.js) - Google Analytics -->

    {{-- <script src="{{ asset('assets/js/app.js') }}"></script> --}}
    <script>
        //$('#flash-overlay-modal').modal();
    </script>

    <script>
        jQuery(document).ready(function() {
            $(document).on('click', '.advance-status-btn', function() {
                const btn = $(this);
                const next = btn.data('next');
                if (!confirm(`Mark order as "${next.replace(/_/g,' ')}"?`)) return;
                $.post(btn.data('url'), {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(() => location.href = btn.data('redirect'))
                    .fail(xhr => alert(xhr.responseJSON?.message ?? 'Error'));
            });

            $(document).on('click', '.cancel-order-btn', function() {
                if (!confirm('Cancel this order?')) return;
                const btn = $(this);
                $.post(btn.data('url'), {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(() => location.href = btn.data('redirect'))
                    .fail(xhr => alert(xhr.responseJSON?.message ?? 'Error'));
            });
        });
    </script>
</body>

</html>
