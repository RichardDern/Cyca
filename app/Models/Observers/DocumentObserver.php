<?php

namespace App\Models\Observers;

use App\Jobs\EnqueueDocumentUpdate;
use App\Models\Document;
use App\Notifications\DocumentUpdated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class DocumentObserver
{
    /**
     * Handle the document "created" event.
     */
    public function created(Document $document)
    {
        EnqueueDocumentUpdate::dispatch($document);
    }

    /**
     * Handle the document "updated" event.
     */
    public function updated(Document $document)
    {
        $usersToNotify = [];

        foreach ($document->folders()->with('user')->get() as $folder) {
            $usersToNotify[] = $folder->user;
        }

        Notification::send($usersToNotify, new DocumentUpdated($document));
    }

    /**
     * Handle the document "deleted" event.
     */
    public function deleted(Document $document)
    {
        Storage::deleteDirectory($document->getStoragePath());
    }

    /**
     * Handle the document "restored" event.
     */
    public function restored(Document $document)
    {
    }

    /**
     * Handle the document "force deleted" event.
     */
    public function forceDeleted(Document $document)
    {
    }
}
