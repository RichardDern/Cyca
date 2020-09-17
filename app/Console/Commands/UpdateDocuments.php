<?php

namespace App\Console\Commands;

use App\Jobs\EnqueueDocumentUpdate;
use App\Models\Document;
use Cache;
use Illuminate\Console\Command;

class UpdateDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enqueue documents that need update';

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
        $oldest = now()->subMinute(config('cyca.maxAge.document'));

        $documents = Document::where('checked_at', '<', $oldest)->get();

        foreach ($documents as $document) {
            $cacheKey = sprintf('queue_document_%d', $document->id);

            if (!Cache::has($cacheKey)) {
                Cache::forever($cacheKey, now());

                EnqueueDocumentUpdate::dispatch($document);
            }
        }

        return 0;
    }
}
