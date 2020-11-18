<?php

namespace App\Models\Policies;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Permission;

class FolderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Folder  $folder
     * @return mixed
     */
    public function view(User $user, Folder $folder)
    {
        return $this->checkFolderAuthorization($user, $folder);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // We will perform real validation in the createIn method below through
        // the Folder/StoreRequest FormRequest as we need the folder we're
        // trying to create a sub-folder to
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Folder  $folder
     * @return mixed
     */
    public function createIn(User $user, Folder $folder)
    {
        if ($folder->type === 'unread_items') {
            return false;
        }

        $permissions = $this->checkFolderAuthorization($user, $folder);
        
        if (!$permissions) {
            return false;
        } elseif ($permissions === true) {
            return true;
        }

        return $permissions->can_create_folder;
    }

    /**
     * Determine whether the user can create a bookmark in specified folder.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Folder  $folder
     * @return mixed
     */
    public function createBookmarkIn(User $user, Folder $folder)
    {
        if ($folder->type === 'unread_items') {
            return false;
        }

        $permissions = $this->checkFolderAuthorization($user, $folder);
        
        if (!$permissions) {
            return false;
        } elseif ($permissions === true) {
            return true;
        }
        
        return $permissions->can_create_document;
    }

    /**
     * Determine whether the user can remove a bookmark from specified folder.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Folder  $folder
     * @return mixed
     */
    public function deleteBookmarkFrom(User $user, Folder $folder)
    {
        if ($folder->type === 'unread_items') {
            return false;
        }
        
        $permissions = $this->checkFolderAuthorization($user, $folder);
        
        if (!$permissions) {
            return false;
        } elseif ($permissions === true) {
            return true;
        }
        
        return $permissions->can_delete_document;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Folder  $folder
     * @return mixed
     */
    public function update(User $user, Folder $folder)
    {
        $permissions = $this->checkFolderAuthorization($user, $folder);
        
        if (!$permissions) {
            return false;
        } elseif ($permissions === true) {
            return true;
        }

        return $permissions->can_update_folder;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Folder  $folder
     * @return mixed
     */
    public function delete(User $user, Folder $folder)
    {
        if ($folder->type !== 'folder') {
            return false;
        }

        $permissions = $this->checkFolderAuthorization($user, $folder);
        
        if (!$permissions) {
            return false;
        } elseif ($permissions === true) {
            return true;
        }
        
        return $permissions->can_delete_folder;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Folder  $folder
     * @return mixed
     */
    public function restore(User $user, Folder $folder)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Folder  $folder
     * @return mixed
     */
    public function forceDelete(User $user, Folder $folder)
    {
        return false;
    }

    /**
     * Determine whether the user can update model's permissions.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Folder  $folder
     * @return mixed
     */
    public function setPermission(User $user, Folder $folder)
    {
        return $folder->group->user_id === $user->id;
    }

    /**
     * Perform common authorization tests for specified user and folder
     *
     * @param \App\Models\User $user
     * @param \App\Models\Folder $folder
     * @return boolean
     */
    private function checkFolderAuthorization(User $user, Folder $folder)
    {
        // Specified user is folder's creator
        if ((int) $folder->user_id === (int) $user->id) {
            return true;
        }

        $group = $this->folderBelongsToActiveUserGroup($user, $folder);

        if (empty($group)) {
            return false;
        }

        if ($group->user_id === $user->id) {
            return true;
        }

        $permissions = $folder->permissions()->where('user_id', $user->id)->first();

        if (!$permissions) {
            $defaultPermissions = $folder->permissions()->whereNull('user_id')->first();

            if (empty($defaultPermissions)) {
                $defaultPermissions = new Permission();

                $defaultPermissions->folder_id           = $folder->id;
                $defaultPermissions->can_create_folder   = false;
                $defaultPermissions->can_update_folder   = false;
                $defaultPermissions->can_delete_folder   = false;
                $defaultPermissions->can_create_document = false;
                $defaultPermissions->can_delete_document = false;

                $defaultPermissions->save();
            }

            return $defaultPermissions;
        }

        return $permissions;
    }

    /**
     * Determine if specified folder belongs to a group in which specified user
     * is active
     *
     * @param \App\Models\User $user
     * @param \App\Models\Folder $folder
     * @return boolean
     */
    private function folderBelongsToActiveUserGroup(User $user, Folder $folder)
    {
        return $user->groups()->active()->find($folder->group_id);
    }
}
