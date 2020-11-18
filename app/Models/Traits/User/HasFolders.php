<?php

namespace App\Models\Traits\User;

use App\Models\Folder;
use App\Models\Group;

trait HasFolders
{
    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Currently selected folder in each group
     *
     * @var array
     */
    protected $selectedFolders = [];

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Folders owned (created) by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdFolders()
    {
        return $this->hasMany(Folder::class);
    }

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Return user's folders as a flat tree.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFlatTree(Group $group = null)
    {
        if (empty($group)) {
            $group = $this->selectedGroup();
        }

        return Folder::getFlatTreeFor($this, $group);
    }

    /**
     * Return the key to access reminded selected folder for this user and
     * specified group
     *
     * @return string
     */
    protected function selectedFolderStoreKey(Group $group)
    {
        if (empty($group)) {
            $group = $this->selectedGroup();
        }

        return sprintf('selectedFolder.%d.%d', $this->id, $group->id);
    }

    /**
     * Return stored user's selected folder in specified group
     *
     * @param \App\Models\Group $group
     * @return \App\Models\Folder
     */
    protected function fetchSelectedFolder(Group $group)
    {
        $key = $this->selectedFolderStoreKey($group);

        if (cache()->has($key)) {
            $folder = $group->folders()->find(cache($key));

            if (!empty($folder)) {
                return $folder;
            }
        }

        return $group->folders()->ofType('root')->first();
    }

    /**
     * Save user's selected folder in specified group
     *
     * @param \App\Models\Group $group
     */
    protected function storeSelectedFolder(Group $group)
    {
        if (empty($group)) {
            $group = $this->selectedGroup();
        }

        $key    = $this->selectedFolderStoreKey($group);
        $folder = $this->selectedFolders[$group->id];

        if (!empty($folder)) {
            cache()->forever($key, $folder->id);
        } else {
            cache()->forget($key);
        }
    }

    /**
     * Return current user's selected folder in specified group
     *
     * @return \App\Models\Group|null
     */
    public function selectedFolder(Group $group = null)
    {
        if (empty($group)) {
            $group = $this->selectedGroup();
        }

        if (empty($this->selectedFolders[$group->id])) {
            $this->selectedFolders[$group->id] = $this->fetchSelectedFolder($group);
        }

        return $this->selectedFolders[$group->id];
    }

    /**
     * Remember user's selected folder in specified (or current) group
     *
     * @param \App\Models\Folder $folder
     * @param \App\Models\Group|null $group
     * @return array
     */
    public function setSelectedFolder(Folder $folder = null, Group $group = null)
    {
        if (empty($group)) {
            $group = $this->selectedGroup();
        }

        $this->selectedFolders[$group->id] = $folder;

        $this->storeSelectedFolder($group);
    }

    /**
     * Return the key to get specified folder's expanded/collased state in
     * specified group
     *
     * @param \App\Models\Folder|null $folder
     * @param \App\Models\Group|null $group
     * @return string
     */
    protected function folderExpandedStoreKey(Folder $folder = null, Group $group = null)
    {
        if (empty($group)) {
            $group = $this->selectedGroup();
        }

        if (empty($folder)) {
            $folder = $this->selectedFolder($group);
        }

        return sprintf('folderExpandedState.%d.%d.%d', $this->id, $group->id, $folder->id);
    }

    /**
     * Return specified folder's expanded/collapsed state in specified group
     *
     * @param \App\Models\Folder|null $folder
     * @param \App\Models\Group|null $group
     * @return boolean
     */
    public function getFolderExpandedState(Folder $folder = null, Group $group = null)
    {
        if (empty($folder)) {
            $folder = $this->selectedFolder($group);
        }

        $key = $this->folderExpandedStoreKey($folder, $group);

        return (bool) cache($key, false);
    }

    /**
     * Set specified folder's expanded/collapsed state in specified group
     *
     * @param boolean $expanded
     * @param \App\Models\Folder|null $folder
     * @param \App\Models\Group|null $group
     * @param boolean $recursive Apply new state recursively
     */
    public function setFolderExpandedState($expanded, Folder $folder = null, Group $group = null, $recursive = false)
    {
        $key = $this->folderExpandedStoreKey($folder, $group);

        cache()->forever($key, (bool) $expanded);

        if ($recursive) {
            foreach ($folder->children as $subFolder) {
                $this->setFolderExpandedState($expanded, $subFolder, $group, $recursive);
            }
        }
    }

    /**
     * Make sure specified folder's ancestor are all expanded, so the folder is
     * visible
     *
     * @param \App\Models\Folder $folder
     */
    public function ensureAncestorsAreExpanded(Folder $folder = null)
    {
        if (empty($folder)) {
            $folder = $this->selectedFolder();
        }

        while ($folder = $folder->parent) {
            $this->setFolderExpandedState(true, $folder);
        }
    }
}
