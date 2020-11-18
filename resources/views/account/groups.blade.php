@extends('layouts.account')

@push('scripts')
<script src="{{ asset('js/groups.js') }}" defer></script>
@endpush

@section('content')
<groups-browser></groups-browser>
@endsection
