<?php

namespace App\Http\Controllers;

use App\Http\Requests\Documents\StoreRequest;
use App\Models\Document;
use App\Models\FeedItemState;
use App\Models\Folder;
use App\Notifications\UnreadItemsChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Document::class, 'document');
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
        $user      = $request->user();
        $url       = $validated['url'];
        $folder    = Folder::find($validated['folder_id']);
        $document  = Document::firstOrCreate(['url' => $url]);

        $folder->documents()->save($document, [
            'initial_url' => $url,
        ]);

        return $folder->listDocuments($user);
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
        $user = $request->user();

        $document->findDupplicatesFor($user);

        $document->loadMissing('feeds')->loadCount(['feedItemStates' => function ($query) use ($user) {
            $query->where('is_read', false)->where('user_id', $user->id);
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
        $this->authorize('createBookmarkIn', $targetFolder);
        $this->authorize('deleteBookmarkFrom', $sourceFolder);

        $bookmarks = $sourceFolder->documents()->whereIn('documents.id', $request->input('documents'))->get();

        foreach ($bookmarks as $bookmark) {
            $sourceFolder->documents()->updateExistingPivot($bookmark->id, ['folder_id' => $targetFolder->id]);
        }

        $usersToNotify = $sourceFolder->group->activeUsers->merge($targetFolder->group->activeUsers);

        Notification::send($usersToNotify, new UnreadItemsChanged([
            'folders' => [
                $sourceFolder->id,
                $targetFolder->id,
            ],
        ]));

        return $request->user()->countUnreadItems([
            'folders' => [
                $sourceFolder->id,
                $targetFolder->id,
            ],
        ]);
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
        $this->authorize('deleteBookmarkFrom', $folder);

        $user      = $request->user();
        $documents = $folder->documents()->whereIn('documents.id', $request->input('documents'))->get();

        foreach ($documents as $document) {
            $folder->documents()->detach($document);
        }

        Notification::send($folder->group->activeUsers()->get(), new UnreadItemsChanged(['folders' => [$folder]]));

        return $folder->listDocuments($user);
    }

    /**
     * Increment visits for specified document in specified folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Document $document
     * @return \Illuminate\Http\Response
     */
    public function visit(Request $request, Document $document)
    {
        $document->visits++;
        $document->save();

        return $this->show($request, $document);
    }
}
