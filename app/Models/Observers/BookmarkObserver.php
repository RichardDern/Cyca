<?php

namespace App\Models\Observers;

use App\Models\Bookmark;
use App\Notifications\UnreadItemsChanged;
use Illuminate\Support\Facades\Notification;

class BookmarkObserver
{
    /**
     * Handle the bookmark "created" event.
     *
     * @param  \App\Models\Bookmark  $bookmark
     * @return void
     */
    public function created(Bookmark $bookmark)
    {
        Notification::send($bookmark->folder->group->activeUsers, new UnreadItemsChanged(['documents' => [$bookmark->document->id]]));

        $bookmark->document->addHistoryEntry('bookmark_created', [
            'user'        => $bookmark->folder->user->toHistoryArray(),
            'breadcrumbs' => $bookmark->folder->breadcrumbs,
            'document'    => $bookmark->document->toHistoryArray(),
        ], $bookmark->folder->user);
    }

    /**
     * Handle the bookmark "deleting" event.
     *
     * @param  \App\Models\Bookmark  $bookmark
     * @return void
     */
    public function deleting(Bookmark $bookmark)
    {
        $bookmark->document->addHistoryEntry('bookmark_deleted', [
            'user'        => $bookmark->folder->user->toHistoryArray(),
            'breadcrumbs' => $bookmark->folder->breadcrumbs,
            'document'    => $bookmark->document->toHistoryArray(),
        ], $bookmark->folder->user);
    }
}
