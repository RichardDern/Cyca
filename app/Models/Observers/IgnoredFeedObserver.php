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
        HistoryEntry::create([
            'user_id' => $ignoredFeed->user->id,
            'feed_id' => $ignoredFeed->feed->id,
            'event' => 'feed_ignored',
            'details' => [
                'user' => $ignoredFeed->user->toHistoryEntry(),
                'feed' => $ignoredFeed->feed->toHistoryEntry()
            ]
        ]);
    }

    /**
     * Handle the ignored feed "deleting" event.
     *
     * @param  \App\Models\IgnoredFeed  $ignoredFeed
     * @return void
     */
    public function deleting(IgnoredFeed  $ignoredFeed)
    {
        HistoryEntry::create([
            'user_id' => $ignoredFeed->user->id,
            'feed_id' => $ignoredFeed->feed->id,
            'event' => 'feed_followed',
            'details' => [
                'user' => $ignoredFeed->user->toHistoryEntry(),
                'feed' => $ignoredFeed->feed->toHistoryEntry()
            ]
        ]);
    }
}
