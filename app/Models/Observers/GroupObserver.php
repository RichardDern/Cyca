<?php

namespace App\Models\Observers;

use App\Models\Group;

class GroupObserver
{
    /**
     * Handle the group "created" event.
     *
     * @param  \App\Models\Group $group
     * @return void
     */
    public function created(Group $group)
    {
        $group->createDefaultFolders();
    }
}
