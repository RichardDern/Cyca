@extends('layouts.auth')

@section('content')
    <div id="account-content-wrapper">
        <form class="w-full flex flex-col" method="POST" action="{{ route('register') }}">
            @csrf

            <label for="name">
                {{ __('User Name') }}:
            </label>

            <input id="name" type="text" class="@error('name')  border-red-500 @enderror" name="name"
                value="{{ old('name') }}" required autocomplete="name" autofocus>

            <label for="email">
                {{ __('E-Mail Address') }}:
            </label>

            <input id="email" type="email" class="@error('email') border-red-500 @enderror" name="email"
                value="{{ old('email') }}" required autocomplete="email">

            <label for="password">
                {{ __('Password') }}:
            </label>

            <input id="password" type="password" class="@error('password') border-red-500 @enderror" name="password"
                required autocomplete="new-password">

            <label for="password-confirm">
                {{ __('Confirm Password') }}:
            </label>

            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">

            <button type="submit" class="info p-2 mt-6">
                {{ __('Register') }}
            </button>

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
