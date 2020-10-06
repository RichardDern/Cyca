@extends('layouts.account')

@push('scripts')
<script src="{{ asset('js/themes-browser.js') }}" defer></script>
@endpush

@section('content')
    <themes-browser></themes-browser>
@endsection
