@section('menu')
<a href="{{ route('login') }}" class="{{ url()->current() === route('login') ? 'selected' : '' }}">{{ __('Login') }}</a>
<a href="{{ route('register') }}" class="{{ url()->current() === route('register') ? 'selected' : '' }}">{{ __('Register') }}</a>
<a href="{{ route('password.request') }}" class="{{ url()->current() === route('password.request') ? 'selected' : '' }}">{{ __('Password lost') }}</a>
@endsection

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="icons-file-url" content="{{ $iconsFileUrl }}">
    <meta name="theme" content="{{ auth()->user()->theme }}">

    <title>{{ config('app.name', 'Cyca') }}</title>

    <link href="{{ $css }}" rel="stylesheet" id="main-stylesheet">

    <script>
        const lang = @json($langStrings);
    </script>

    <script src="{{ asset('js/themes-browser.js') }}" defer></script>

    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
</head>

<body>
    <main id="app">
        <div id="account-menu">
            <div class="w-full">
                <h1 class="text-white tracking-wide text-4xl">
                    {{ config('app.name') }}
                </h1>
                <div id="account-menu-items">
                    @yield('menu')
                </div>
            </div>
        </div>
        <div id="account-content">
            <div id="account-content-wrapper">
                @yield('content')
            </div>
        </div>
    </main>
</body>

</html>
