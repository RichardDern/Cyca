<?php

namespace App\Models\Policies;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Folder $folder)
    {
        return $this->checkFolderAuthorization($user, $folder);
    }

    /**
     * Determine whether the user can create models.
     *
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
     * @return mixed
     */
    public function createIn(User $user, Folder $folder)
    {
        if ($folder->type === 'unread_items') {
            return false;
        }

        return $this->checkFolderAuthorization($user, $folder, 'can_create_folder');
    }

    /**
     * Determine whether the user can create a bookmark in specified folder.
     *
     * @return mixed
     */
    public function createBookmarkIn(User $user, Folder $folder)
    {
        if ($folder->type === 'unread_items') {
            return false;
        }

        return $this->checkFolderAuthorization($user, $folder, 'can_create_document');
    }

    /**
     * Determine whether the user can remove a bookmark from specified folder.
     *
     * @return mixed
     */
    public function deleteBookmarkFrom(User $user, Folder $folder)
    {
        if ($folder->type === 'unread_items') {
            return false;
        }

        return $this->checkFolderAuthorization($user, $folder, 'can_delete_document');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Folder $folder)
    {
        return $this->checkFolderAuthorization($user, $folder, 'can_update_folder');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Folder $folder)
    {
        if ($folder->type !== 'folder') {
            return false;
        }

        return $this->checkFolderAuthorization($user, $folder, 'can_delete_folder');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return mixed
     */
    public function restore(User $user, Folder $folder)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Folder $folder)
    {
        return false;
    }

    /**
     * Determine whether the user can update model's permissions.
     *
     * @return mixed
     */
    public function setPermission(User $user, Folder $folder)
    {
        return $folder->group->user_id === $user->id;
    }

    /**
     * Check if specified user is the creator of specified folder.
     *
     * @return bool
     */
    private function hasCreatedFolder(User $user, Folder $folder)
    {
        return (int) $folder->user_id === (int) $user->id;
    }

    /**
     * Return a boolean value indicating if specified user has created the group
     * specified folder belongs to.
     */
    private function userCreatedFolderGroup(User $user, Folder $folder)
    {
        $group = $this->folderBelongsToActiveUserGroup($user, $folder);

        if (!empty($group)) {
            return $group->user_id === $user->id;
        }

        return false;
    }

    /**
     * Perform common authorization tests for specified user and folder.
     *
     * @return array
     */
    private function checkFolderAuthorization(User $user, Folder $folder, string $ability = null)
    {
        if ($this->hasCreatedFolder($user, $folder)) {
            return true;
        }

        if ($this->userCreatedFolderGroup($user, $folder)) {
            return true;
        }

        $permissions = $folder->permissions()->where('user_id', $user->id)->first();

        if (!$permissions) {
            $defaultPermissions = $folder->permissions()->whereNull('user_id')->first();

            if (empty($defaultPermissions)) {
                $defaultPermissions = $folder->setDefaultPermission();
            }

            $permissions = $defaultPermissions;
        }

        if ($ability) {
            return $permissions->{$ability};
        }

        return false;
    }

    /**
     * Determine if specified folder belongs to a group in which specified user
     * is active.
     *
     * @return \App\Models\Group
     */
    private function folderBelongsToActiveUserGroup(User $user, Folder $folder)
    {
        return $user->groups()->active()->find($folder->group_id);
    }
}
