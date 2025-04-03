<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-topbar="light" data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">
    @include('layouts.partials.style')
</head>

@yield('body')

@yield('content')

@include('layouts.partials.scripts')
@stack('js')
</body>

</html>
