@extends('layouts.account')

@section('content')
    <div id="account-content-wrapper">
        @if (session('status') === 'profile-information-updated')
            <div class="alert success">{{ __('Your profile has been updated') }}</div>
        @endif

        <form method="POST" class="w-full flex flex-col" action="/user/profile-information">
            @method('PUT')
            @csrf

            <label for="name">
                {{ __('User Name') }}:
            </label>

            <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                autocomplete="name" autofocus>

            @error('name')
            <div class="alert error">
                {{ $message }}
            </div>
            @enderror

            <label for="email">
                {{ __('E-Mail Address') }}:
            </label>

            <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                autocomplete="email">

            @error('email')
            <div class="alert error">
                {{ $message }}
            </div>
            @enderror

            @if (!empty(
            auth()
                ->user()
                ->hasVerifiedEmail()
        ))
                <div class="alert success">{{ __('E-mail address verified on :email_verified_at', [
                        'email_verified_at' => auth()->user()->email_verified_at->isoFormat('LLLL'),
                    ]) }}</div>
            @else
                <div class="alert warning">{{ __('Awaiting e-mail address confirmation') }}</div>
            @endif

            <label for="lang">
                {{ __('Language') }}:
            </label>

            <select name="lang" id="lang">
                @foreach (config('lang') as $code => $name)
                    <option value="{{ $code }}" {{ auth()->user()->lang === $code ? 'selected' : '' }}>{{ $name }}
                    </option>
                @endforeach
            </select>

            @error('lang')
            <div class="alert error">
                {{ $message }}
            </div>
            @enderror

            <button type="submit" class="info p-2 mt-6">{{ __('Save') }}</button>
        </form>

        @if (!auth()
            ->user()
            ->hasVerifiedEmail())
            <a href="#" class="button info p-2 mt-12"
                onclick="event.preventDefault(); document.getElementById('resend-verification-form').submit();">{{ __('Send email verification link') }}</a>

            <form id="resend-verification-form" method="POST" action="{{ route('verification.send') }}" class="hidden">
                @csrf
            </form>
        @endif
    </div>
@endsection
