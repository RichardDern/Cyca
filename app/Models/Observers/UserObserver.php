<?php

namespace App\Models\Observers;

use App\Models\User;

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
        $user->createDefaultFolders();
        $user->importInitialData();
    }
}
