<?php

namespace App\Models\Observers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use App\Jobs\EnqueueDocumentUpdate;

class DocumentObserver
{
    /**
     * Handle the document "created" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function created(Document $document)
    {
        EnqueueDocumentUpdate::dispatch($document);
    }

    /**
     * Handle the document "updated" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function updated(Document $document)
    {
        //
    }

    /**
     * Handle the document "deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function deleted(Document $document)
    {
        Storage::deleteDirectory($document->getStoragePath());
    }

    /**
     * Handle the document "restored" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function restored(Document $document)
    {
        //
    }

    /**
     * Handle the document "force deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function forceDeleted(Document $document)
    {
        //
    }
}
