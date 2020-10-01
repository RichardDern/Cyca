<?php

namespace App\Http\Controllers;

use App\Http\Requests\Documents\StoreRequest;
use App\Models\FeedItemState;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $folder = $request->user()->folders()->where('is_selected', true)->first();

        return $folder->listDocuments();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Documents\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $url = urldecode($validated['url']);

        $folder = $request->user()->folders()->findOrFail($validated['folder_id']);

        if ($folder->type !== 'folder' && $folder->type !== 'root') {
            abort(422);
        }

        $document = Document::firstOrCreate(['url' => $url]);

        $folder->documents()->save($document, [
            'initial_url' => $url,
        ]);

        return $folder->listDocuments();
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Document $document)
    {
        $document->findDupplicatesFor($request->user());

        $document->loadMissing('feeds')->loadCount(['feedItemStates' => function ($query) use ($request) {
                $query->where('is_read', false)->where('user_id', $request->user()->id);
            }]);

        return $document;
    }

    /**
     * Move document into specified folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Folder $folder
     * @return \Illuminate\Http\Response
     */
    public function move(Request $request, Folder $sourceFolder, Folder $targetFolder)
    {
        if ($sourceFolder->user_id !== $request->user()->id || $targetFolder->user_id !== $request->user()->id) {
            abort(404);
        }

        $documents = $sourceFolder->documents()->whereIn('documents.id', $request->input('documents'))->get();

        foreach ($documents as $document) {
            $sourceFolder->documents()->updateExistingPivot($document->id, ['folder_id' => $targetFolder->id]);
        }

        FeedItemState::where('folder_id', $sourceFolder->id)->whereIn('document_id', $request->input('documents'))->update(['folder_id' => $targetFolder->id]);
    }

    /**
     * Remove documents from specified folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Folder $folder
     * @return \Illuminate\Http\Response
     */
    public function destroyBookmarks(Request $request, Folder $folder)
    {
        if ($folder->user_id !== $request->user()->id) {
            abort(404);
        }

        $documents = $folder->documents()->whereIn('documents.id', $request->input('documents'))->get();

        foreach ($documents as $document) {
            $folder->documents()->detach($document);
        }

        return $folder->listDocuments();
    }

    /**
     * Increment visits for specified document in specified folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Document $document
     * @param  Folder $folder
     * @return \Illuminate\Http\Response
     */
    public function visit(Request $request, Document $document, Folder $folder)
    {
        if ($folder->user_id !== $request->user()->id) {
            abort(404);
        }

        if($folder->type === 'unread_items') {
            $folders = $document->findDupplicatesFor($request->user());

            foreach($folders as $folder) {
                $doc = $folder->documents()->where('documents.id', $document->id)->first();

                $folder->documents()->updateExistingPivot($doc->id, ['visits' => $doc->bookmark->visits + 1]);
            }
        } else {
            $document = $folder->documents()->where('documents.id', $document->id)->first();

            $folder->documents()->updateExistingPivot($document->id, ['visits' => $document->bookmark->visits + 1]);
        }

        return $document;
    }
}
