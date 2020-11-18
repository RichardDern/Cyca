<?php

namespace App\Models\Observers;

use App\Models\Folder;
use App\Notifications\UnreadItemsChanged;
use Illuminate\Support\Facades\Notification;

class FolderObserver
{
    /**
     * Handle the folder "created" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function created(Folder $folder)
    {
        $folder->addHistoryEntry('folder_created', [
            'user'        => $folder->user->toHistoryArray(),
            'breadcrumbs' => $folder->breadcrumbs,
        ], $folder->user);

        //TODO: Add history entries for users in the same group
    }

    /**
     * Handle the folder "deleting" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function deleting(Folder $folder)
    {
        $folder->addHistoryEntry('folder_deleted', [
            'user'        => $folder->user->toHistoryArray(),
            'breadcrumbs' => $folder->breadcrumbs,
        ], $folder->user);

        //TODO: Add history entries for users in the same group
    }

    /**
     * Handle the folder "deleted" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function deleted(Folder $folder)
    {
        Notification::send($folder->group->activeUsers, new UnreadItemsChanged(['folders' => [$folder]]));
    }
}
