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
        $group->addHistoryEntry('group_created', [
            'user'  => $group->creator->toHistoryArray(),
            'group' => $group->toHistoryArray(),
        ], $group->creator);

        $group->createDefaultFolders();
    }
}
