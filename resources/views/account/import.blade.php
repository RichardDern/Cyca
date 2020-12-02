@extends('layouts.account')

@push('scripts')
<script src="{{ asset('js/import.js') }}" defer></script>
@endpush

@section('content')
<div class="my-auto w-1/4 p-6">
    <importer v-bind:available-importers='@json(config('importers.adapters'))'></importer>
</div>
@endsection