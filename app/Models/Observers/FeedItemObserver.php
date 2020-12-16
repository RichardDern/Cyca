<?php

namespace App\Models\Observers;

use App\Models\FeedItem;
use Illuminate\Support\Facades\Storage;

class FeedItemObserver
{
    /**
     * Handle the feed item "deleted" event.
     */
    public function deleted(FeedItem $feedItem)
    {
        Storage::deleteDirectory($feedItem->getStoragePath());
    }
}
