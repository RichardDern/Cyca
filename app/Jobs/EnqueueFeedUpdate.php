<?php

namespace App\Jobs;

use App\Models\Feed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnqueueFeedUpdate implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Feed to update.
     *
     * @var \App\Models\Feed
     */
    protected $feed;

    /**
     * Create a new job instance.
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->feed->analyze();
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->feed->id;
    }
}
