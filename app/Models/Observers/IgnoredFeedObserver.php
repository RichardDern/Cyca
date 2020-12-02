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
        
    }

    /**
     * Handle the ignored feed "deleting" event.
     *
     * @param  \App\Models\IgnoredFeed  $ignoredFeed
     * @return void
     */
    public function deleting(IgnoredFeed  $ignoredFeed)
    {
        
    }
}
