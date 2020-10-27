<?php

namespace App\Models\Observers;

use App\Models\User;
use App\Models\HistoryEntry;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $user->addHistoryEntry('user_created', ['user' => $user->toHistoryArray()], $user);
        $user->createDefaultFolders();
        $user->importInitialData();
    }
}
