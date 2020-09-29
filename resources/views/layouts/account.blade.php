@extends('layouts.auth')

@section('menu')
    <a href="{{ route('home') }}" class="py-4">{{ __('Back') }}</a>
    <a href="{{ route('account') }}" class="{{ url()->current() === route('account') ? 'selected' : '' }}">{{ __('My account') }}</a>
    <a href="{{ route('export') }}" class="{{ url()->current() === route('export') ? 'selected' : '' }}">{{ __('Export my data') }}</a>
    <a href="{{ route('import.form') }}" class="{{ url()->current() === route('import.form') ? 'selected' : '' }}">{{ __('Import data') }}</a>
@endsection
