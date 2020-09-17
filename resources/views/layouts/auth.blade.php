<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cyca') }}</title>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>

<body class="bg-gray-700 h-screen antialiased leading-none">
    <div class="flex h-screen">
        <div class="h-screen bg-gray-800 w-1/3 pr-6 flex items-center text-right">
            <div class="w-full">
                <h1 class="text-white tracking-wide text-4xl">
                    {{ config('app.name') }}
                </h1>
                <div class="mt-12 flex items-stretch flex-col">
                    <a href="{{ route('login') }}" class="text-{{ url()->current() === route('login') ? 'blue-500' : 'gray-300' }} py-2">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="text-{{ url()->current() === route('register') ? 'blue-500' : 'gray-300' }} py-2">{{ __('Register') }}</a>
                    <a href="{{ route('password.request') }}" class="text-{{ url()->current() === route('password.request') ? 'blue-500' : 'gray-300' }} py-2">{{ __('Password lost') }}</a>
                </div>
            </div>
        </div>
        <div class="w-1/4 h-screen flex items-center pl-6">
            @yield('content')
        </div>
    </div>
</body>

</html>
