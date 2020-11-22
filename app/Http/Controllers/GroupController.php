<?php

namespace App\Http\Controllers;

use App\Http\Requests\Groups\StoreRequest;
use App\Http\Requests\Groups\UpdateRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Groups\InviteUserRequest;
use Notification;
use App\Notifications\InvitedToJoinGroup;
use App\Notifications\AsksToJoinGroup;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Group::class, 'group');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user   = $request->user();
        $search = $request->input('search');

        $query = Group::visible()
            ->whereNotIn('id', $user->groups->pluck('id'))
            ->with('creator:id,name')
            ->withCount('activeUsers');

        if (!empty($search)) {
            $query = $query->where('groups.name', 'like', '%' . $search . '%');
        }

        return $query
            ->orderBy('name')
            ->simplePaginate(25);
    }

    /**
     * Display a listing of the resource (active groups)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function indexActive(Request $request)
    {
        $user = $request->user();

        return $user->listActiveGroups();
    }

    /**
     * Display a listing of the resource (my groups)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function indexMyGroups(Request $request)
    {
        $user = $request->user();

        return $user->groups()->withCount('activeUsers', 'pendingUsers')
            ->whereNotIn('status', [
                Group::$STATUS_REJECTED,
                Group::$STATUS_LEFT
            ])->orderBy('position')->orderBy('id')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $validated            = $request->validated();
        $user                 = $request->user();
        $validated['user_id'] = $user->id;
        $group                = Group::create($validated);

        $user->groups()->save($group, [
            'status' => 'created',
        ]);

        return $user->groups()->withCount('activeUsers')->orderBy('position')->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Group $group)
    {
        $user = $request->user();

        $user->setSelectedGroup($group);

        return $user->getFlatTree($group);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Groups\UpdateRequest  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Group $group)
    {
        $validated = $request->validated();

        $group->name              = $validated['name'];
        $group->description       = $validated['description'];
        $group->invite_only       = $validated['invite_only'];
        $group->auto_accept_users = $validated['auto_accept_users'];

        $group->save();

        return $request->user()->groups()->withCount('activeUsers', 'pendingUsers')->find($group->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Group $group)
    {
        $user = $request->user();

        $group->delete();

        return $user->groups()->withCount('activeUsers')->get();
    }

    /**
     * Update my groups positions
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePositions(Request $request)
    {
        if (!$request->has('positions')) {
            abort(422);
        }

        $positions = $request->input('positions');

        if (!is_array($positions)) {
            abort(422);
        }

        $user = $request->user();

        foreach ($positions as $groupId => $position) {
            if (!is_numeric($groupId) || !is_numeric($position)) {
                abort(422);
            }

            $group = $user->groups()->findOrFail($groupId);

            $user->groups()->updateExistingPivot($group, ['position' => $position]);
        }

        return $user->groups()->withCount('activeUsers')->orderBy('position')->get();
    }

    /**
     * Invite user to join specified group
     *
     * @param  \App\Requests\Groups\InviteUserRequest  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function inviteUser(InviteUserRequest $request, Group $group)
    {
        $user = $request->user();

        if (!$user->can('invite', $group)) {
            abort(403);
        }

        $validated   = $request->validated();
        $invitedUser = User::where('email', $validated['email'])->first();

        if ($invitedUser) {
            $invitedUser->updateGroupStatus($group, Group::$STATUS_INVITED);
        }

        Notification::route('mail', $validated['email'])
            ->notify(new InvitedToJoinGroup($request->user(), $group));

        //TODO: Add history entry

        return $request->user()->groups()->withCount('activeUsers', 'pendingUsers')->find($group->id);
    }

    public function acceptInvitation(Request $request, Group $group)
    {
        $user = $request->user();
        $user->updateGroupStatus($group, Group::$STATUS_ACCEPTED);

        //TODO: Add history entry

        if ($request->ajax()) {
            return $user->groups()->withCount('activeUsers', 'pendingUsers')->find($group->id);
        } else {
            return redirect()->route('account.groups');
        }
    }

    public function approveUser(Request $request, Group $group, User $user)
    {
        $creator = $request->user();

        if (!$creator->can('approve', $group)) {
            abort(403);
        }

        $user->updateGroupStatus($group, Group::$STATUS_ACCEPTED);

        //TODO: Add history entry

        return redirect()->route('account.groups');
    }

    public function rejectInvitation(Request $request, Group $group)
    {
        $user = $request->user();
        $user->updateGroupStatus($group, Group::$STATUS_REJECTED);

        //TODO: Add history entry

        return $user->groups()->withCount('activeUsers', 'pendingUsers')->find($group->id);
    }

    public function leave(Request $request, Group $group)
    {
        $user = $request->user();
        $user->groups()->detach($group);

        //TODO: Add history entry
    }

    public function join(Request $request, Group $group)
    {
        $user = $request->user();

        if ($group->auto_accept_users) {
            $user->updateGroupStatus($group, Group::$STATUS_ACCEPTED);
        } else {
            $user->updateGroupStatus($group, Group::$STATUS_JOINING);

            Notification::route('mail', $group->creator->email)
                ->notify(new AsksToJoinGroup($request->user(), $group));
        }

        //TODO: Add history entry
    }
}
