<?php

namespace App\Models\Observers;

use App\Models\Folder;
use App\Notifications\UnreadItemsChanged;
use Illuminate\Support\Facades\Notification;

class FolderObserver
{
    /**
     * Handle the folder "created" event.
     */
    public function created(Folder $folder)
    {
    }

    /**
     * Handle the folder "deleting" event.
     */
    public function deleting(Folder $folder)
    {
    }

    /**
     * Handle the folder "deleted" event.
     */
    public function deleted(Folder $folder)
    {
        Notification::send($folder->group->activeUsers, new UnreadItemsChanged(['folders' => [$folder]]));
    }
}
