<?php

namespace App\Models\Observers;

use App\Models\IgnoredFeed;
use App\Models\HistoryEntry;

class IgnoredFeedObserver
{
    /**
     * Handle the ignored feed "created" event.
     *
     * @param  \App\Models\IgnoredFeed  $ignoredFeed
     * @return void
     */
    public function created(IgnoredFeed  $ignoredFeed)
    {
        $ignoredFeed->feed->addHistoryEntry('feed_ignored', [
            'user' => $ignoredFeed->user->toHistoryArray(),
            'feed' => $ignoredFeed->feed->toHistoryArray()
        ], $ignoredFeed->user);
    }

    /**
     * Handle the ignored feed "deleting" event.
     *
     * @param  \App\Models\IgnoredFeed  $ignoredFeed
     * @return void
     */
    public function deleting(IgnoredFeed  $ignoredFeed)
    {
        $ignoredFeed->feed->addHistoryEntry('feed_followed', [
            'user' => $ignoredFeed->user->toHistoryArray(),
            'feed' => $ignoredFeed->feed->toHistoryArray()
        ], $ignoredFeed->user);
    }
}
