@extends('layouts.auth')

@section('content')
    <div id="account-content-wrapper">
        <form class="w-full flex flex-col" method="POST" action="{{ route('password.email') }}">
            @csrf

            <label for="email">
                {{ __('E-Mail Address') }}:
            </label>

            <input id="email" type="email" class="@error('email') border-red-500 @enderror" name="email"
                value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
            <p class="text-red-500 text-xs italic mt-4">
                {{ $message }}
            </p>
            @enderror

            <button type="submit" class="info p-2 mt-6">
                {{ __('Send Password Reset Link') }}
            </button>

            @if (session('status'))
                <p class="text-sm text-green-500 mt-4" role="alert">
                    {{ session('status') }}
                </p>
            @endif
        </form>
    </div>
@endsection
