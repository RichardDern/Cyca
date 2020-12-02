@extends('layouts.account')

@section('content')
<div class="my-auto w-1/4 p-6">
    @if (session('status') === 'password-updated')
    <div class="alert success">{{ __('Your profile has been updated') }}</div>
    @endif

    <form method="POST" class="w-full flex flex-col" action="/user/password">
        @method('PUT')
        @csrf

        <div class="form-group">
            <label for="current-password">
                {{ __('Current password') }}:
            </label>

            <input id="current-password" type="password" name="current_password" required />
        </div>

        @error('current_password')
        <div class="alert error">
            {{ $message }}
        </div>
        @enderror

        <div class="form-group">
            <label for="new-password">
                {{ __('New password') }}:
            </label>

            <input id="new-password" type="password" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="password-confirm">
                {{ __('Confirm Password') }}:
            </label>

            <input id="password-confirm" type="password" name="password_confirmation" required
                autocomplete="new-password">
        </div>

        @error('password_confirmation')
        <div class="alert error">
            {{ $message }}
        </div>
        @enderror

        <div class="form-group last">
            <button type="submit">
                → {{ __('Update password') }}
            </button>
        </div>
    </form>
</div>
@endsection