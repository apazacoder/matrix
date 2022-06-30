<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title') </title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon"/>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('/apple-touch-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('/apple-touch-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('/apple-touch-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/apple-touch-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('/apple-touch-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('/apple-touch-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('/apple-touch-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('/apple-touch-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/apple-touch-icon-180x180.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('/favicon-16x16.png') }}" sizes="16x16">
    <link rel="icon" type="image/png" href="{{ asset('/favicon-32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/png" href="{{ asset('/android-chrome-192x192.png') }}" sizes="192x192">
    <meta name="msapplication-square70x70logo" content="{{ asset('/smalltile.png') }}"/>
    <meta name="msapplication-square150x150logo" content="{{ asset('/mediumtile.png') }}"/>
    <meta name="msapplication-wide310x150logo" content="{{ asset('/widetile.png') }}"/>
    <meta name="msapplication-square310x310logo" content="{{ asset('/largetile.png') }}"/>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>

<div id="loader-wrapper" style="position: fixed;  width: 100%; height: 100%; background: #F7F7F8; z-index: 300">
    <div class="loader">
        <div class="loader-inner ball-scale-multiple">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST"
      style="display: none;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
@yield('content')

<script defer type="text/javascript" src="{{ mix('js/app.js') }}"></script>
@yield('scripts')
@if (session()->has('message'))
    <div id="message" style="display:none"> {{ session()->get('message') }} </div>
    <script>
        document.onreadystatechange = function () {
            if (document.readyState === "complete") {
                setTimeout(function () {
                    showWarningMessage($("#message").text());
                }, 1500);
            }
        }
    </script>
@endif
</body>
</html>
