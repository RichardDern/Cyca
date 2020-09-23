@extends('layouts.auth')

@section('content')
    <div class="w-full">

        @if (session('resent'))
            <div class="text-sm border border-t-8 rounded text-green-700 border-green-600 bg-green-100  px-3 py-4 mb-4"
                role="alert">
                {{ __('A fresh verification link has been sent to your email address.') }}
            </div>
        @endif

                <p class="leading-normal text-white">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                </p>

                <p class="leading-normal mt-6 text-white">
                    {{ __('If you did not receive the email') }}, <a
                        class="text-blue-500 hover:text-blue-700 no-underline cursor-pointer"
                        onclick="event.preventDefault(); document.getElementById('resend-verification-form').submit();">{{ __('click here to request another') }}</a>.
                </p>

                <form id="resend-verification-form" method="POST" action="{{ route('verification.send') }}"
                    class="hidden">
                    @csrf
                </form>
    </div>
@endsection
