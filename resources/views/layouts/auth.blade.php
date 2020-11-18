@extends('layouts.app')

@section('menu')
<a href="{{ route('login') }}" class="{{ url()->current() === route('login') ? 'selected' : '' }}">{{ __('Login') }}</a>
<a href="{{ route('register') }}" class="{{ url()->current() === route('register') ? 'selected' : '' }}">{{ __('Register') }}</a>
<a href="{{ route('password.request') }}" class="{{ url()->current() === route('password.request') ? 'selected' : '' }}">{{ __('Password lost') }}</a>
@endsection

@section('main_content')
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
    @yield('content')
</div>
@endsection
