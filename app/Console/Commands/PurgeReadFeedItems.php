<?php

namespace App\Console\Commands;

use App\Models\FeedItem;
use Illuminate\Console\Command;

class PurgeReadFeedItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feeditems:purgeread';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge old read feed items from the database';

    /**
     * Create a new command instance.
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
        $oldest       = now()->subDays(config('cyca.maxOrphanAge.feeditems'));
        $oldFeedItems = FeedItem::allRead()->olderThan($oldest)->get();

        // We need to do this individually to take advantage of the
        // FeedItemObserver and automatically delete associated files that may
        // have been locally stored
        foreach ($oldFeedItems as $item) {
            $item->delete();
        }

        return 0;
    }
}
