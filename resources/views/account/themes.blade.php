@extends('layouts.account')

@section('content')
    <form id="themes-browser" method="POST" action="{{ route('account.setTheme') }}">
        @csrf
        <input type="hidden" name="theme" id="theme" value="{{ auth()->user()->theme }}" />

        @foreach ($availableThemes as $dirname => $availableTheme)
            <div class="card {{ $dirname === auth()->user()->theme ? 'selected' : '' }}" onclick="document.getElementById('theme').value = '{{ $dirname }}';document.getElementById('themes-browser').submit();">
                <h2>
                    <div>{{ $availableTheme['name'] }}</div>
                    <div>
                        <a title="{{ __('Preview') }}" href="{{ route('home', ['theme' => $dirname]) }}"
                            target="_blank"><svg fill="currentColor" width="16" height="16" class="favicon">
                                <use xlink:href="{{ $iconsFileUrl }}#unread_items" />
                            </svg></a>
                    </div>
                </h2>
                <img src="/themes/{{ $dirname }}/{{ $availableTheme['screenshot'] }}" />
                <p class="meta">
                    {{ __('Created by') }}: <a href="{{ $availableTheme['url'] }}" target="_blank"
                        rel="noopener noreferrer">{{ $availableTheme['author'] }}</a>
                </p>
            </div>
        @endforeach
    </form>
@endsection
