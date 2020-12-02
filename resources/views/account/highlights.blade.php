@extends('layouts.account')

@push('scripts')
<script src="{{ asset('js/highlights.js') }}" defer></script>
@endpush

@section('content')
    <highlights></highlights>
@endsection