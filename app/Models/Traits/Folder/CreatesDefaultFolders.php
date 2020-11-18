<?php

namespace App\Models\Traits\Folder;

use App\Models\Group;
use App\Models\User;

trait CreatesDefaultFolders
{

    # --------------------------------------------------------------------------
    # ----| Constants |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Position of the unread items folder in the folders hierarchy
     */
    private static $POSITION_UNREAD_ITEMS = 0;

    /**
     * Position of the root folder in the folders hierarchy
     */
    private static $POSITION_ROOT = 1;

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Create default folders for specified group. This method should be called
     * only once when group is created.
     *
     * @throws \App\Exceptions\UserDoesNotExistsException
     * @param \App\Models\User $user User creating the folders
     * @param \App\Models\Group $group
     */
    public static function createDefaultFoldersFor(User $user, Group $group)
    {
        $group->folders()->saveMany([
            new self([
                'type'     => 'unread_items',
                'title'    => 'Unread items',
                'position' => self::$POSITION_UNREAD_ITEMS,
                'user_id'  => $user->id,
            ]),
            new self([
                'type'     => 'root',
                'title'    => 'Root',
                'position' => self::$POSITION_ROOT,
                'user_id'  => $user->id,
            ]),
        ]);

        session([
            sprintf('selectedFolder.%d', $group->id) =>
            $group->folders()->ofType('root')->first()->id,
        ]);
    }
}
