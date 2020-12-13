<?php

namespace App\Console\Commands;

use App\Jobs\EnqueueDocumentUpdate;
use App\Models\Document;
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

        $documents = Document::where('checked_at', '<', $oldest)->orWhereNull('checked_at')->get();
        $count     = 0;

        foreach ($documents as $document) {
            EnqueueDocumentUpdate::dispatch($document);
            $count++;
        }

        $this->line(sprintf('%s document(s) queued for update', $count));

        return 0;
    }
}
