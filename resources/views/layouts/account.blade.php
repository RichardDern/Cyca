<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cyca') }}</title>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
</head>

<body class="bg-gray-700 h-screen antialiased leading-none">
    <div class="flex h-screen">
        <div class="h-screen bg-gray-800 w-1/3 pr-6 flex items-center text-right">
            <div class="w-full text-right">
                <h1 class="text-white tracking-wide text-4xl">
                    {{ config('app.name') }}
                </h1>
                <div class="mt-12 flex items-stretch flex-col">
                    <a href="{{ route('home') }}" class="text-gray-300 py-4 hover:text-gray-100">{{ __('Back') }}</a>
                    <a href="{{ route('account') }}" class="text-{{ url()->current() === route('account') ? 'blue-500' : 'gray-300 hover:text-gray-100' }} py-2">{{ __('My account') }}</a>
                    <a href="{{ route('export') }}" class="text-{{ url()->current() === route('export') ? 'blue-500' : 'gray-300 hover:text-gray-100' }} py-2">{{ __('Export my data') }}</a>
                    <a href="{{ route('import.form') }}" class="text-{{ url()->current() === route('import.form') ? 'blue-500' : 'gray-300 hover:text-gray-100' }} py-2">{{ __('Import data') }}</a>
                </div>
            </div>
        </div>
        <div class="w-1/4 h-screen flex items-center pl-6">
            @yield('content')
        </div>
    </div>
</body>

</html>
