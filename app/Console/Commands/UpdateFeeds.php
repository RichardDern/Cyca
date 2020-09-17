<?php

namespace App\Console\Commands;

use App\Jobs\EnqueueFeedUpdate;
use App\Models\Feed;
use Cache;
use Illuminate\Console\Command;

class UpdateFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enqueue feeds that need update';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldest = now()->subMinute(config('cyca.maxAge.feed'));

        $feeds = Feed::where('checked_at', '<', $oldest)->orWhereNull('checked_at')->get();

        foreach ($feeds as $feed) {
            $cacheKey = sprintf('queue_feed_%d', $feed->id);

            if (!Cache::has($cacheKey)) {
                Cache::forever($cacheKey, now());

                EnqueueFeedUpdate::dispatch($feed);
            }
        }

        return 0;
    }
}
