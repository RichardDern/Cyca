<?php

namespace App\Models\Observers;

use App\Models\Bookmark;
use App\Models\HistoryEntry;

class BookmarkObserver
{
    /**
     * Handle the bookmark "created" event.
     *
     * @param  \App\Models\Bookmark  $bookmark
     * @return void
     */
    public function created(Bookmark  $bookmark)
    {
        HistoryEntry::create([
            'user_id' => $bookmark->folder->user->id,
            'folder_id' => $bookmark->folder->id,
            'document_id' => $bookmark->document->id,
            'event' => 'bookmark_created',
            'details' => [
                'user' => $bookmark->folder->user->toHistoryEntry(),
                'folder' => $bookmark->folder->toHistoryEntry(),
                'document' => $bookmark->document->toHistoryEntry()
            ]
        ]);
    }

    /**
     * Handle the bookmark "deleting" event.
     *
     * @param  \App\Models\Bookmark  $bookmark
     * @return void
     */
    public function deleting(Bookmark  $bookmark)
    {
        HistoryEntry::create([
            'user_id' => $bookmark->folder->user->id,
            'folder_id' => $bookmark->folder->id,
            'document_id' => $bookmark->document->id,
            'event' => 'bookmark_deleted',
            'details' => [
                'user' => $bookmark->folder->user->toHistoryEntry(),
                'folder' => $bookmark->folder->toHistoryEntry(),
                'document' => $bookmark->document->toHistoryEntry()
            ]
        ]);
    }
}
