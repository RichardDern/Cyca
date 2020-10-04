@extends('layouts.auth')

@section('content')
    <div id="account-content-wrapper">
        <form class="w-full flex flex-col" method="POST" action="{{ route('password.update') }}">
            @csrf

            <input id="email" type="hidden" name="email" value="{{ request()->input('email') }}" required>
            <input id="token" type="hidden" name="token" value="{{ request()->route('token') }}" required>

            <label for="password">
                {{ __('New password') }}:
            </label>

            <input id="password" type="password" class="@error('password') border-red-500 @enderror" name="password"
                autofocus required autocomplete="new-password">

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

            @if (session('status'))
                <p class="text-sm text-green-500 mt-4" role="alert">
                    {{ session('status') }}
                </p>
            @endif
        </form>
    </div>
@endsection
