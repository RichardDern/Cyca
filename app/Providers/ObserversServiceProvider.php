<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\User;
use App\Models\Observers\DocumentObserver;
use App\Models\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Feed;
use App\Models\Observers\FeedObserver;

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
    }
}
