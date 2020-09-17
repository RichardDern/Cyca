@extends('layouts.app')

@section('content')
    <form class="w-full flex flex-col" method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <p class="leading-normal">
            {{ __('Please confirm your password before continuing.') }}
        </p>

        <label for="password">
            {{ __('Password') }}:
        </label>

        <input id="password" type="password" class="@error('password') border-red-500 @enderror" name="password" required
            autocomplete="new-password">

        <button type="submit" class="info p-2 mt-6">
            {{ __('Confirm Password') }}
        </button>

        @error('password')
        <p class="text-red-500 text-xs italic mt-4">
            {{ $message }}
        </p>
        @enderror
    </form>
@endsection
