<?php

namespace App\Models\Observers;

use App\Models\Feed;
use App\Jobs\EnqueueFeedUpdate;

class FeedObserver
{
    /**
     * Handle the feed "created" event.
     *
     * @param  \App\Models\Feed  $feed
     * @return void
     */
    public function created(Feed $feed)
    {
        EnqueueFeedUpdate::dispatch($feed);
    }

    /**
     * Handle the feed "updated" event.
     *
     * @param  \App\Models\Feed  $feed
     * @return void
     */
    public function updated(Feed $feed)
    {
        //
    }

    /**
     * Handle the feed "deleted" event.
     *
     * @param  \App\Models\Feed  $feed
     * @return void
     */
    public function deleted(Feed $feed)
    {
        //
    }

    /**
     * Handle the feed "restored" event.
     *
     * @param  \App\Models\Feed  $feed
     * @return void
     */
    public function restored(Feed $feed)
    {
        //
    }

    /**
     * Handle the feed "force deleted" event.
     *
     * @param  \App\Models\Feed  $feed
     * @return void
     */
    public function forceDeleted(Feed $feed)
    {
        //
    }
}
