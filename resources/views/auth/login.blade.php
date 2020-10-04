@extends('layouts.auth')

@section('content')
    <div id="account-content-wrapper">
        <form class="w-full flex flex-col" method="POST" action="{{ route('login') }}">
            @csrf

            <label for="email">
                {{ __('E-Mail Address') }}:
            </label>

            <input id="email" type="email" class="@error('email') border-red-500 @enderror" name="email"
                value="{{ old('email') }}" required autocomplete="email" autofocus>

            <label for="password">
                {{ __('Password') }}:
            </label>

            <input id="password" type="password" class="@error('password') border-red-500 @enderror" name="password"
                required>

            <label for="remember">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="ml-2">{{ __('Remember Me') }}</span>
            </label>

            <button type="submit" class="info p-2 mt-6">
                {{ __('Login') }}
            </button>

            @error('email')
            <p class="text-red-500 text-xs italic mt-4">
                {{ $message }}
            </p>
            @enderror

            @error('password')
            <p class="text-red-500 text-xs italic mt-4">
                {{ $message }}
            </p>
            @enderror
        </form>
    </div>
@endsection
