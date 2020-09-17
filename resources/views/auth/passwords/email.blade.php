@extends('layouts.auth')

@section('content')

    @if (session('status'))
        <div class="text-sm border border-t-8 rounded text-green-700 border-green-600 bg-green-100 px-3 py-4 mb-4"
            role="alert">
            {{ session('status') }}
        </div>
    @endif

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
    </form>
@endsection
