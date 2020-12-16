<?php

namespace App\Models\Observers;

use App\Models\IgnoredFeed;

class IgnoredFeedObserver
{
    /**
     * Handle the ignored feed "created" event.
     */
    public function created(IgnoredFeed $ignoredFeed)
    {
    }

    /**
     * Handle the ignored feed "deleting" event.
     */
    public function deleting(IgnoredFeed $ignoredFeed)
    {
    }
}
