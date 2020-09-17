<?php

namespace App\Models\Traits\Folder;

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

    /**
     * Position of the account link in the folders hierarchy
     */
    private static $POSITION_ACCOUNT = 2;

    /**
     * Position of the log out link in the folders hierarchy
     */
    private static $POSITION_LOGOUT = 3;

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Create default folders for specified user. This method should be called
     * only once when user is created.
     *
     * @throws \App\Exceptions\UserDoesNotExistsException
     * @param \App\Models\User $user
     */
    public static function createDefaultFoldersFor(User $user)
    {
        if (empty($user->id)) {
            throw new \App\Exceptions\UserDoesNotExistsException("Cannot create default folders for inexisting user");
        }

        $user->folders()->saveMany([
            new self([
                'type'        => 'unread_items',
                'title'       => 'Unread items',
                'position'    => self::$POSITION_UNREAD_ITEMS,
                'is_selected' => false,
                'is_expanded' => true
            ]),
            new self([
                'type'        => 'root',
                'title'       => 'Root',
                'position'    => self::$POSITION_ROOT,
                'is_selected' => true,
                'is_expanded' => true
            ]),
            new self([
                'type'        => 'account',
                'title'       => 'My account',
                'position'    => self::$POSITION_ACCOUNT,
                'is_selected' => false,
                'is_expanded' => true
            ]),
            new self([
                'type'        => 'logout',
                'title'       => 'Log out',
                'position'    => self::$POSITION_LOGOUT,
                'is_selected' => false,
                'is_expanded' => true
            ]),
        ]);
    }
}
