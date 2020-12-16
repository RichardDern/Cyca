<?php

namespace App\Models\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     */
    public function created(User $user)
    {
        $group = $user->createOwnGroup();

        $user->importInitialData($group);
    }
}
