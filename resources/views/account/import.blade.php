@extends('layouts.account')

@push('scripts')
<script src="{{ asset('js/import.js') }}" defer></script>
@endpush

@section('content')
    <div id="account-content-wrapper">
        <div class="w-full flex flex-col">
            <importer></importer>
        </div>
    </div>
@endsection
