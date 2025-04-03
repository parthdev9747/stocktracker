<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('css/tabler.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/tabler-flags.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/tabler-socials.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/tabler-payments.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/tabler-vendors.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/tabler-marketing.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/demo.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/inter.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/common.css') }}" rel="stylesheet" />
</head>

<body class=" d-flex flex-column">
    <div class="page">
        {{ $slot }}
    </div>
    <script src="{{ asset('js/tabler.min.js') }}" defer></script>
    <script src="{{ asset('js/demo.min.js') }}" defer></script>
    <script src="{{ asset('js/demo-theme.min.js') }}"></script>
</body>

</html>
