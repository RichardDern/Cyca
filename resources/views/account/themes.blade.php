@extends('layouts.account')

@push('scripts')
<script src="{{ asset('js/themes-browser.js') }}" defer></script>
@endpush

@section('content')
    <div id="account-content-wrapper">
        <themes-browser></themes-browser>
    </div>
@endsection
