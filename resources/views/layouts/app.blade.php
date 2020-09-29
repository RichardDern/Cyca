<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->user()->id }}">

    <title>{{ config('app.name', 'Cyca') }}</title>

    <script>
        const lang = @json($langStrings);
    </script>

    <script src="{{ asset('js/app.js') }}" defer></script>

    <link href="{{ mix(sprintf('themes/%s.css', auth()->user()->theme)) }}" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
</head>

<body>
    @yield('content')
</body>

</html>
