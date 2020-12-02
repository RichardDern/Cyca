@extends('layouts.app')

@section('menu')
<a href="{{ route('login') }}" class="{{ url()->current() === route('login') ? 'selected' : '' }}">{{ __('Login') }}</a>
<a href="{{ route('register') }}"
    class="{{ url()->current() === route('register') ? 'selected' : '' }}">{{ __('Register') }}</a>
<a href="{{ route('password.request') }}"
    class="{{ url()->current() === route('password.request') ? 'selected' : '' }}">{{ __('Password lost') }}</a>
@endsection

@section('main_content')
<div class="w-1/4 p-6 bg-gray-200 dark:bg-gray-900 text-right flex h-screen overflow-auto">
    <div class="m-auto w-full">
        <h1 class="tracking-wide text-4xl text-gray-600 dark:text-white">
            {{ config('app.name') }}
        </h1>
        <div class="vertical list mt-6 items-rounded spaced">
            @yield('menu')
        </div>
    </div>
</div>
<div class="w-3/4 bg-gray-100 dark:bg-gray-800 flex h-screen overflow-auto">
    @yield('content')
</div>
@endsection