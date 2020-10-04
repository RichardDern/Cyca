@extends('layouts.account')

@section('content')
    <div id="account-content-wrapper">
        @if (session('status') === 'password-updated')
            <div class="alert success">{{ __('Your profile has been updated') }}</div>
        @endif

        <form method="POST" class="w-full flex flex-col" action="/user/password">
            @method('PUT')
            @csrf

            <label for="current-password">
                {{ __('Current password') }}:
            </label>

            <input id="current-password" type="password" name="current_password" required />

            @error('current_password')
            <div class="alert error">
                {{ $message }}
            </div>
            @enderror

            <label for="new-password">
                {{ __('New password') }}:
            </label>

            <input id="new-password" type="password" required autocomplete="new-password">

            <label for="password-confirm">
                {{ __('Confirm Password') }}:
            </label>

            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">

            @error('password_confirmation')
            <div class="alert error">
                {{ $message }}
            </div>
            @enderror

            <button type="submit" class="info p-2 mt-6">
                {{ __('Update password') }}
            </button>
        </form>
    </div>
@endsection
