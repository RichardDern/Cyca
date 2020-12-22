@extends('layouts.app')

@push('scripts')
<script src="{{ asset('js/app.js') }}" defer></script>
@endpush

@section('main_content')
<folders-tree class="sm:w-3/12 xl:w-2/12 flex-none h-screen bg-gray-150 dark:bg-gray-900" v-on:groups-loaded="onGroupsLoaded"
    v-on:folders-loaded="onFoldersLoaded" v-on:selected-folder-changed="onSelectedFolderChanged"
    v-on:selected-group-changed="onSelectedGroupChanged" v-on:item-dropped="onItemDropped"></folders-tree>
<documents-list class="flex-grow h-screen overflow-auto bg-gray-100 dark:bg-gray-850"
    v-on:selected-documents-changed="onSelectedDocumentsChanged"></documents-list>
<div class="sm:w-5/12 xl:w-7/12 flex-none h-screen flex flex-col bg-gray-50 dark:bg-gray-800">
    <feed-items-list class="h-2/6 overflow-auto" v-on:selected-feeditems-changed="onSelectedFeedItemsChanged">
    </feed-items-list>
    <div class="h-4/6">
        <component v-bind:is="detailsViewComponent" v-on:feeditems-read="onFeedItemsRead"
            v-on:folder-selected="onSelectedFolderChanged" v-on:document-added="onDocumentAdded"
            v-on:documents-deleted="onDocumentsDeleted"></component>
    </div>
</div>
@endsection