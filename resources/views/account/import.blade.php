@extends('layouts.account')

@section('content')
    <div id="account-content-wrapper">
        <div class="w-full flex flex-col">
            <form method="POST" class="w-full flex flex-col" action="{{ route('account.import') }}" enctype="multipart/form-data">
                @csrf

                <label for="name">
                    {{ __('File to import') }}:
                </label>

                <input id="file" type="file" class="@error('name')  border-red-500 @enderror" name="file" required />

                <div class="text-blue-400 mt-4">
                    <p>
                        {{ __('You can import the following types of file') }}:
                    </p>

                    <ul class="mt-2">
                        <li>{{ __('Cyca export') }}</li>
                    </ul>
                </div>

                <button type="submit" class="info p-2 mt-6">{{ __('Import') }}</button>

                @error('file')
                <p class="text-red-500 text-xs italic mt-4">
                    {{ $message }}
                </p>
                @enderror
            </form>
        </div>
    </div>
@endsection
