<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}" defer="defer">

    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('css/main.css') }}">

    <title>Elpis View Educational Services</title>

    @stack('styles')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    <style defer="defer">
        .login_header {
            background-color: #ffffff !important;
        }
        .change-lang {
            background-color: #e8eef3;
            padding: 0 3px 0 3px;
        }
    </style>

    @if(file_exists(public_path().'/css/login-custom.css'))
        <link href="{{ asset('css/login-custom.css') }}" rel="stylesheet">
    @endif

</head>

<body>

<header class="px-4 bg-white sticky-top d-flex justify-content-center align-items-center login_header">
    <i class="fa fa-graduation-cap mr-2" style="font-size:24px;color:#4e73df;"></i>
    <h3 class="mb-0 pl-1">Elpis View</h3>
</header>

<section class="py-5 bg-grey login_section">
    <div class="container">
        <div class="row">
            <div class="text-center col-md-12">

                <div class="mx-auto text-center bg-white rounded login_box">
                    {{ $slot }}
                </div>

                {{ $outsideLoginBox ?? '' }}

            </div>
        </div>

    </div>

</section>

<!-- Font Awesome -->
<script src="{{ asset('vendor/jquery/all.min.js') }}" defer="defer"></script>

<!-- Template JS -->
<script src="{{ asset('js/main.js') }}"></script>
<script>
    document.loading = 'Loading';
    const MODAL_DEFAULT = '#myModalDefault';
    const MODAL_LG = '#myModal';
    const MODAL_XL = '#myModalXl';
    const MODAL_HEADING = '#modelHeading';
    const RIGHT_MODAL = '#task-detail-1';
    const RIGHT_MODAL_CONTENT = '#right-modal-content';
    const RIGHT_MODAL_TITLE = '#right-modal-title';
</script>

{{ $scripts ?? '' }}

</body>

</html>
