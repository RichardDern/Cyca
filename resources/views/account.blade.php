@extends('layouts.account')

@section('content')
    <div class="w-full flex flex-col">
        <form method="POST" class="w-full flex flex-col" action="user/profile-information">
            @method('PUT')
            @csrf

            <label for="name">
                {{ __('User Name') }}:
            </label>

            <input id="name" type="text" class="@error('name')  border-red-500 @enderror" name="name"
                value="{{ old('name', auth()->user()->name) }}" required autocomplete="name" autofocus>

            <label for="email">
                {{ __('E-Mail Address') }}:
            </label>

            <input id="email" type="email" class="@error('email') border-red-500 @enderror" name="email"
                value="{{ old('email', auth()->user()->email) }}" required autocomplete="email" autofocus>

            @if (!empty(
            auth()
                ->user()
                ->hasVerifiedEmail()
        ))
                <p class="text-green-500 text-xs italic mt-4">{{ __('E-mail address verified on :email_verified_at', [
                        'email_verified_at' => auth()->user()->email_verified_at->isoFormat('LLLL'),
                    ]) }}</p>
            @else
                <p class="text-yellow-500 text-xs italic mt-4">
                    {{ __('Awaiting e-mail address confirmation') }}</p>
            @endif

            <label for="lang">
                {{ __('Language') }}:
            </label>

            <select name="lang" id="lang">
                @foreach(config('lang') as $code => $name)
                <option value="{{ $code }}" {{ auth()->user()->lang === $code ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <label for="theme">
                {{ __('Theme') }}:
            </label>

            <select name="theme" id="theme">
                @foreach($themes as $theme)
                <option value="{{ $theme }}" {{ auth()->user()->theme === $theme ? 'selected' : '' }}>{{ $theme }}</option>
                @endforeach
            </select>

            <button type="submit" class="info p-2 mt-6">{{ __('Save') }}</button>

            @error('name')
            <p class="text-red-500 text-xs italic mt-4">
                {{ $message }}
            </p>
            @enderror
        </form>

        @if(!auth()->user()->hasVerifiedEmail())
            <a href="#" class="button info p-2 mt-12"
                onclick="event.preventDefault(); document.getElementById('resend-verification-form').submit();">{{ __('Send email verification link') }}</a>

            <form id="resend-verification-form" method="POST" action="{{ route('verification.send') }}" class="hidden">
                @csrf
            </form>
        @endif

        <form method="POST" class="w-full flex flex-col mt-12" action="user/password">
            @method('PUT')
            @csrf

            <label for="current-password">
                {{ __('Current password') }}:
            </label>

            <input id="current-password" type="password" class="@error('current_password') border-red-500 @enderror" name="current_password" required />

            <label for="new-password">
                {{ __('New password') }}:
            </label>

            <input id="new-password" type="password" class="@error('password') border-red-500 @enderror" name="password" required
                autocomplete="new-password">

            <label for="password-confirm">
                {{ __('Confirm Password') }}:
            </label>

            <input id="password-confirm" type="password" class="" name="password_confirmation" required
                autocomplete="new-password">

            <button type="submit" class="info p-2 mt-6">
                {{ __('Update password') }}
            </button>

            @if (session('status'))
                <p class="text-sm text-green-500 mt-4"
                    role="alert">
                    {{ session('status') }}
                </p>
            @endif

            @error('current_password')
            <p class="text-red-500 text-xs italic mt-4">
                {{ $message }}
            </p>
            @enderror

            @error('password_confirmation')
            <p class="text-red-500 text-xs italic mt-4">
                {{ $message }}
            </p>
            @enderror
        </form>
    </div>
@endsection
