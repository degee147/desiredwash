<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>Desired Wash</title>
    <meta name="keywords"
        content="laundry pickup service, laundry delivery, dry cleaning service, on-demand laundry, laundry app Nigeria, doorstep laundry service, wash and fold service, Desired Wash">
    <meta name="description"
        content="Desired Wash is a convenient laundry pickup and delivery service that lets you schedule pickups, track orders, and pay securely online. Fast, reliable, and hassle-free laundry solutions.">
    <meta name="author" content="Desired Wash">

    <link rel="shortcut icon" href="images/favicon.ico">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no,maximum-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <nav class="panel-menu" id="mobile-menu">
        <ul></ul>
        <div class="mm-navbtn-names">
            <div class="mm-closebtn">Close</div>
            <div class="mm-backbtn">Back</div>
        </div>
    </nav>
    @include('partials.top-header')



    <main id="tt-pageContent">
        {{ $slot }}
    </main>
    @include('partials.footer')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script async src="js/bundle.js"></script><!-- modal - Make an Appointment -->
    @include('partials.pickup-modal')
    {{-- @include('partials.color-swatch') --}}
</body>

</html>
