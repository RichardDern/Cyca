<?php

namespace App\Models\Observers;

use App\Models\Group;

class GroupObserver
{
    /**
     * Handle the group "created" event.
     */
    public function created(Group $group)
    {
        $group->createDefaultFolders();
    }
}
