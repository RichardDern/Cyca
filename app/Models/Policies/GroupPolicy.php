<?php

namespace App\Models\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        return $this->checkGroupAuthorization($user, $group);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        return $group->user_id === $user->id;
    }

    /**
     * Determine whether the user can invite someone into specified group.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function invite(User $user, Group $group)
    {
        return $this->checkGroupAuthorization($user, $group, [
            Group::$STATUS_OWN,
            Group::$STATUS_CREATED,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return $group->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function restore(User $user, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function forceDelete(User $user, Group $group)
    {
        //
    }

    /**
     * Perform common authorization tests for specified user and group
     *
     * @param \App\Models\User $user
     * @param \App\Models\Group $group
     * @return boolean
     */
    private function checkGroupAuthorization(User $user, Group $group, $statuses = [])
    {
        // Specified user is group's creator
        if ($group->user_id === $user->id) {
            return true;
        }

        $userGroup = $user->groups()->active()->find($group->id);

        if (!empty($userGroup)) {
            return true;
        }

        if (!empty($statuses) && in_array($userGroup->pivot->status, $statuses)) {
            return true;
        }

        return false;
    }
}
