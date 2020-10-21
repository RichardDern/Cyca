@extends('layouts.account')

@push('scripts')
<script src="{{ asset('js/history.js') }}" defer></script>
@endpush

@section('content')
<history-browser></history-browser>
@endsection
