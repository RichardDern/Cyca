@extends('layouts.app')

@section('content')
    <div id="app">
        <div id="content">
            <folders-tree v-on:folders-loaded="onFoldersLoaded" v-on:selected-folder-changed="onSelectedFolderChanged"
                v-on:item-dropped="onItemDropped"></folders-tree>
            <documents-list v-on:selected-documents-changed="onSelectedDocumentsChanged"></documents-list>
            <div id="right-panel">
                <feed-items-list v-on:selected-feeditems-changed="onSelectedFeedItemsChanged"></feed-items-list>
                <div id="details-view">
                    <component v-bind:is="detailsViewComponent" v-on:feeditems-read="onFeedItemsRead"
                        v-on:folder-selected="onSelectedFolderChanged" v-on:document-added="onDocumentAdded" v-on:documents-deleted="onDocumentsDeleted"></component>
                </div>
            </div>
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
@endsection
