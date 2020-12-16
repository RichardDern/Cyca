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
        $oldest    = now()->subMinute(config('cyca.maxAge.document'));
        $documents = Document::needingUpdate($oldest)->get();

        foreach ($documents as $document) {
            EnqueueDocumentUpdate::dispatch($document);
        }

        return 0;
    }
}
