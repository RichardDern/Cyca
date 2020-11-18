<?php

namespace App\Models\Traits\User;

use App\Models\Group;

trait HasGroups
{
    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Currently selected group
     *
     * @var \App\Models\Group
     */
    protected $selectedGroup = null;

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Groups created by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdGroups()
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Associated groups
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_groups')->withPivot(['status', 'position']);
    }

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Create and return user's primary group
     *
     * @return \App\Models\Group
     */
    public function createOwnGroup()
    {
        $group = Group::create([
            'name'        => $this->name,
            'invite_only' => true,
            'user_id'     => $this->id,
        ]);

        $this->groups()->attach($group, [
            'status' => Group::$STATUS_OWN,
        ]);

        return $group;
    }

    /**
     * Return the key to access reminded selected group for this user
     *
     * @return string
     */
    protected function selectedGroupStoreKey()
    {
        return sprintf('selectedGroup.%d', $this->id);
    }

    /**
     * Return stored user's selected group
     *
     * @return \App\Models\Group
     */
    protected function fetchSelectedGroup()
    {
        $key = $this->selectedGroupStoreKey();

        if (cache()->has($key)) {
            $group = $this->groups()->active()->find(cache($key));

            if (!empty($group)) {
                return $group;
            }
        }

        return $this->groups()->own()->first();
    }

    /**
     * Save user's selected group
     */
    protected function storeSelectedGroup()
    {
        $key = $this->selectedGroupStoreKey();

        cache()->forever($key, $this->selectedGroup->id);
    }

    /**
     * Return current user's selected group
     *
     * @return \App\Models\Group
     */
    public function selectedGroup()
    {
        if ($this->selectedGroup === null) {
            $this->selectedGroup = $this->fetchSelectedGroup();
        }

        return $this->selectedGroup;
    }

    /**
     * Remember user's selected group
     *
     * @param \App\Models\Group $group
     */
    public function setSelectedGroup(Group $group)
    {
        $this->selectedGroup = $group;

        $this->storeSelectedGroup();

        return $this->getFlatTree();
    }

    /**
     * Return a list of groups user is active in
     *
     * @return \Illuminate\Support\Collection
     */
    public function listActiveGroups()
    {
        $userId = $this->id;

        $groups = $this->groups()
            ->select([
                'groups.id',
                'groups.name',
            ])->active()
            ->orderBy('position')->get();

        return $groups;
    }

    /**
     * Update group status for current user. If group is not associated with
     * user, association will be made. It won't change user group if it is
     * marked as being owned or created by current user, unless $force is true
     *
     * @param \App\Models\Group $group
     * @param string $newStatus
     */
    public function updateGroupStatus(Group $group, $newStatus, $force = false)
    {
        $userGroup = $this->groups()->find($group->id);

        if ($userGroup) {
            if (in_array($userGroup->pivot->status, [Group::$STATUS_OWN, Group::$STATUS_CREATED]) && !$force) {
                return;
            }

            $this->groups()->updateExistingPivot($group->id, [
                'status' => $newStatus
            ]);
        } else {
            $this->groups()->save($group, [
                'status' => $newStatus
            ]);
        }
    }
}
