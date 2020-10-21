<?php

namespace App\Models\Observers;

use App\Models\Folder;
use App\Models\HistoryEntry;

class FolderObserver
{
    /**
     * Handle the folder "created" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function created(Folder  $folder)
    {
        HistoryEntry::create([
            'user_id' => $folder->user->id,
            'folder_id' => $folder->id,
            'event' => 'folder_created',
            'details' => [
                'user' => $folder->user->toHistoryEntry(),
                'folder' => $folder->toHistoryEntry()
            ]
        ]);
    }

    /**
     * Handle the folder "deleting" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function deleting(Folder $folder) {
        HistoryEntry::create([
            'user_id' => $folder->user->id,
            'folder_id' => $folder->id,
            'event' => 'folder_deleted',
            'details' => [
                'user' => $folder->user->toHistoryEntry(),
                'folder' => $folder->toHistoryEntry()
            ]
        ]);
    }
}
