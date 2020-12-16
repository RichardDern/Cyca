<?php

namespace App\Models\Observers;

use App\Jobs\EnqueueFeedUpdate;
use App\Models\Feed;

class FeedObserver
{
    /**
     * Handle the feed "created" event.
     */
    public function created(Feed $feed)
    {
        EnqueueFeedUpdate::dispatch($feed);
    }

    /**
     * Handle the feed "updated" event.
     */
    public function updated(Feed $feed)
    {
    }

    /**
     * Handle the feed "deleted" event.
     */
    public function deleted(Feed $feed)
    {
    }

    /**
     * Handle the feed "restored" event.
     */
    public function restored(Feed $feed)
    {
    }

    /**
     * Handle the feed "force deleted" event.
     */
    public function forceDeleted(Feed $feed)
    {
    }
}
