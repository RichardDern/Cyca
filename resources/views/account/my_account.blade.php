@extends('layouts.account')

@section('content')
<div class="my-auto w-1/4 p-6">
    @if (session('status') === 'profile-information-updated')
    <div class="alert success">{{ __('Your profile has been updated') }}</div>
    @endif

    <form method="POST" action="/user/profile-information">
        @method('PUT')
        @csrf

        <div class="form-group">
            <label for="name">
                {{ __('User Name') }}:
            </label>

            <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                autocomplete="name" autofocus>
        </div>

        @error('name')
        <div class="alert error">
            {{ $message }}
        </div>
        @enderror

        <div class="form-group">
            <label for="email">
                {{ __('E-Mail Address') }}:
            </label>

            <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                autocomplete="email">
        </div>

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
        <p class="text-green-500 italic">{{ __('E-mail address verified on :email_verified_at', [
                        'email_verified_at' => auth()->user()->email_verified_at->isoFormat('LLLL'),
                    ]) }}</p>
        @else
        <p class="text-orange-500 italic">{{ __('Awaiting e-mail address confirmation') }}</p>
        @endif

        <div class="form-group mt-4">
            <label for="lang">
                {{ __('Language') }}:
            </label>

            <select name="lang" id="lang">
                @foreach (config('lang') as $code => $name)
                <option value="{{ $code }}" {{ auth()->user()->lang === $code ? 'selected' : '' }}>{{ $name }}
                </option>
                @endforeach
            </select>
        </div>

        @error('lang')
        <div class="alert error">
            {{ $message }}
        </div>
        @enderror

        <div class="form-group mt-4">
            <label>
                {{ __('Theme') }}:
            </label>

            <div class="theme-selector">
                <label>
                    <input type="radio" name="theme" value="light" {{ auth()->user()->theme === 'light' ? 'checked' : ""
                    }} onchange="localStorage.theme = 'light'; setTheme();" />
                    <div class="ml-1">{{__("Light")}}</div>
                </label>
                <label>
                    <input type="radio" name="theme" value="dark" {{ auth()->user()->theme === 'dark' ? 'checked' : ""
                    }} onchange="localStorage.theme = 'dark'; setTheme();" />
                    <div class="ml-1">{{__("Dark")}}</div>
                </label>
                <label>
                    <input type="radio" name="theme" value="auto" {{ !auth()->user()->theme || auth()->user()->theme ===
                    'auto' ? 'checked' : "" }} onchange="localStorage.removeItem('auto'); setTheme();" />
                    <div class="ml-1">{{__("Auto")}}</div>
                </label>
            </div>
        </div>

        @error('theme')
        <div class="alert error">
            {{ $message }}
        </div>
        @enderror

        <div class="form-group last">
            <button type="submit">→ {{ __('Save') }}</button>
        </div>
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