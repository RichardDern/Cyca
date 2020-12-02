@extends('layouts.auth')

@section('content')
<div class="my-auto w-1/4 p-6">
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">
                {{ __('User Name') }}:
            </label>

            <input id="name" type="text" class="@error('name')  border-red-500 @enderror" name="name"
                value="{{ old('name') }}" required autocomplete="name" autofocus>
        </div>

        <div class="form-group">
            <label for="email">
                {{ __('E-Mail Address') }}:
            </label>

            <input id="email" type="email" class="@error('email') border-red-500 @enderror" name="email"
                value="{{ old('email') }}" required autocomplete="email">
        </div>

        <div class="form-group">
            <label for="password">
                {{ __('Password') }}:
            </label>

            <input id="password" type="password" class="@error('password') border-red-500 @enderror" name="password"
                required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="password-confirm">
                {{ __('Confirm Password') }}:
            </label>

            <input id="password-confirm" type="password" name="password_confirmation" required
                autocomplete="new-password">
        </div>

        <div class="form-group last">
            <button type="submit">
                → {{ __('Register') }}
            </button>
        </div>

        @error('name')
        <p class="text-red-500 text-xs italic mt-4">
            {{ $message }}
        </p>
        @enderror

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