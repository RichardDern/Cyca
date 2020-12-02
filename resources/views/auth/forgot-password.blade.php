@extends('layouts.auth')

@section('content')
<div class="my-auto w-1/4 p-6">
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">
                {{ __('E-Mail Address') }}:
            </label>

            <input id="email" type="email" class="@error('email') border-red-500 @enderror" name="email"
                value="{{ old('email') }}" required autocomplete="email" autofocus>
        </div>

        <div class="form-group last">
            <button type="submit">
                → {{ __('Send Password Reset Link') }}
            </button>
        </div>

        @error('email')
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