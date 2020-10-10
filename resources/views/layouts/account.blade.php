@extends('layouts.auth')

@section('menu')
    <a href="{{ route('home') }}" class="py-4">{{ __('Back') }}</a>
    <a href="{{ route('account') }}" class="{{ url()->current() === route('account') ? 'selected' : '' }}">{{ __('My account') }}</a>
    <a href="{{ route('account.password') }}" class="{{ url()->current() === route('account.password') ? 'selected' : '' }}">{{ __('Update password') }}</a>
    <a href="{{ route('account.theme') }}" class="{{ url()->current() === route('account.theme') ? 'selected' : '' }}">{{ __('Theme') }}</a>
    <a href="{{ route('account.highlights') }}" class="{{ url()->current() === route('account.highlights') ? 'selected' : '' }}">{{ __('Highlights') }}</a>
    <a href="{{ route('account.import.form') }}" class="{{ url()->current() === route('account.import.form') ? 'selected' : '' }}">{{ __('Import data') }}</a>
    <a href="{{ route('account.export') }}" class="{{ url()->current() === route('account.export') ? 'selected' : '' }}">{{ __('Export my data') }}</a>
    <a href="{{ route('account.about') }}" class="{{ url()->current() === route('account.about') ? 'selected' : '' }}">{{ __('About Cyca') }}</a>
@endsection
