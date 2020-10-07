<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />

    <meta name="application-name" content="Cyca" />
    <meta name="author" content="Richard Dern" />
    <meta name="description" content="Bookmarks and feeds manager" />
    <meta name="robots" content="noindex,nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme" content="{{ $activeTheme }}" />
    <meta name="icons-file-url" content="{{ $iconsFileUrl }}" />

    @auth
        <meta name="user-id" content="{{ auth()->user()->id }}" />
    @endauth

    <link rel="icon" href="{{ asset('images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ $css }}" />

    <title>{{ config('app.name', 'Cyca') }}</title>

    <script>
        const lang = @json($langStrings);

    </script>

    @stack('scripts')
</head>

<body>
    <main id="app" role="main">
        @yield('main_content')
    </main>
</body>

</html>
