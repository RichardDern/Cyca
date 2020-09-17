@extends('layouts.auth')

@section('content')
    <form class="w-full flex flex-col" method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email">
            {{ __('E-Mail Address') }}:
        </label>

        <input id="email" type="email" class="@error('email') border-red-500 @enderror" name="email"
            value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

        <label for="password">
            {{ __('Password') }}:
        </label>

        <input id="password" type="password" class="@error('password') border-red-500 @enderror" name="password" required
            autocomplete="new-password">

        <label for="password-confirm">
            {{ __('Confirm Password') }}:
        </label>

        <input id="password-confirm" type="password" class="" name="password_confirmation" required
            autocomplete="new-password">

        <button type="submit" class="info p-2 mt-6">
            {{ __('Reset Password') }}
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
@endsection
