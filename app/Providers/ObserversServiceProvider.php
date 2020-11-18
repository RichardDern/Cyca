<?php

namespace App\Providers;

use App\Models\Bookmark;
use App\Models\Document;
use App\Models\Feed;
use App\Models\Folder;
use App\Models\Group;
use App\Models\IgnoredFeed;
use App\Models\Observers\BookmarkObserver;
use App\Models\Observers\DocumentObserver;
use App\Models\Observers\FeedObserver;
use App\Models\Observers\FolderObserver;
use App\Models\Observers\GroupObserver;
use App\Models\Observers\IgnoredFeedObserver;
use App\Models\Observers\UserObserver;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class ObserversServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Document::observe(DocumentObserver::class);
        Feed::observe(FeedObserver::class);
        Folder::observe(FolderObserver::class);
        Bookmark::observe(BookmarkObserver::class);
        IgnoredFeed::observe(IgnoredFeedObserver::class);
        Group::observe(GroupObserver::class);
    }
}
