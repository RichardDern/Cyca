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
        $bookmark->document->addHistoryEntry('bookmark_created', [
            'user' => $bookmark->folder->user->toHistoryArray(),
            'folder' => $bookmark->folder->toHistoryArray(),
            'document' => $bookmark->document->toHistoryArray()
        ], $bookmark->folder->user);
    }

    /**
     * Handle the bookmark "deleting" event.
     *
     * @param  \App\Models\Bookmark  $bookmark
     * @return void
     */
    public function deleting(Bookmark  $bookmark)
    {
        $bookmark->document->addHistoryEntry('bookmark_deleted', [
            'user' => $bookmark->folder->user->toHistoryArray(),
            'folder' => $bookmark->folder->toHistoryArray(),
            'document' => $bookmark->document->toHistoryArray()
        ], $bookmark->folder->user);
    }
}
