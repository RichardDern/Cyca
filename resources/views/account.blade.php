@extends('layouts.account')

@section('content')
    <div class="w-full flex flex-col">
        <form method="POST" class="w-full flex flex-col" action="{{ route('account.store') }}">
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

            <button type="submit" class="info p-2 mt-6">{{ __('Save') }}</button>

            @error('name')
            <p class="text-red-500 text-xs italic mt-4">
                {{ $message }}
            </p>
            @enderror
        </form>

        <a href="#" class="button info p-2 mt-12"
            onclick="event.preventDefault(); document.getElementById('resend-verification-form').submit();">{{ __('Send email verification link') }}</a>

        <form id="resend-verification-form" method="POST" action="{{ route('verification.resend') }}" class="hidden">
            @csrf
        </form>

        <a href="{{ route('password.request') }}" class="button info p-2 mt-2">{{ __('Send Password Reset Link') }}</a>
    </div>
@endsection
