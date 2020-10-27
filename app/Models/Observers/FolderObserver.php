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
        $folder->addHistoryEntry('folder_created', [
            'user' => $folder->user->toHistoryArray(),
            'folder' => $folder->toHistoryArray(),
            'breadcrumbs' => !empty($folder->parent_id) ? $folder->parent->breadcrumbs : null
        ], $folder->user);
    }

    /**
     * Handle the folder "deleting" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function deleting(Folder $folder) {
        $folder->addHistoryEntry('folder_deleted', [
            'user' => $folder->user->toHistoryArray(),
            'folder' => $folder->toHistoryArray(),
            'breadcrumbs' => !empty($folder->parent_id) ? $folder->parent->breadcrumbs : null
        ], $folder->user);
    }
}
