<?php

namespace App\Models\Observers;

use App\Models\FeedItem;
use Illuminate\Support\Facades\Storage;

class FeedItemObserver
{
    /**
     * Handle the feed item "deleted" event.
     *
     * @param  \App\Models\FeedItem  $feedItem
     * @return void
     */
    public function deleted(FeedItem  $feedItem)
    {
        Storage::deleteDirectory($feedItem->getStoragePath());
    }
}
