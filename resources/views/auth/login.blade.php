@extends('layouts.auth')

@section('content')
<div class="my-auto w-1/4 p-6">
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">
                {{ __('E-Mail Address') }}:
            </label>

            <input id="email" type="email" class="@error('email') border-red-500 @enderror" name="email"
                value="{{ old('email') }}" required autocomplete="email" autofocus>
        </div>

        <div class="form-group">
            <label for="password">
                {{ __('Password') }}:
            </label>

            <input id="password" type="password" class="@error('password') border-red-500 @enderror" name="password"
                required>
        </div>

        <div class="form-group">
            <label for="remember">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="ml-2">{{ __('Remember Me') }}</span>
            </label>
        </div>

        <div class="form-group last">
            <button type="submit">
                → {{ __('Login') }}
            </button>
        </div>

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