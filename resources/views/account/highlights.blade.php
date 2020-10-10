@extends('layouts.account')

@push('scripts')
<script src="{{ asset('js/highlights.js') }}" defer></script>
@endpush

@section('content')
    <div id="account-content-wrapper" class="large">
        <h2>{{ __("Highlights") }}</h2>
        <highlights></highlights>
    </div>
@endsection
