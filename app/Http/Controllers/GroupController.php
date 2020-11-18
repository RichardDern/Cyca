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
        $user = $request->user();

        return Group::visible()->whereNotIn('id', $user->groups->pluck('id'))->withCount('activeUsers')->simplePaginate(25);
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

        $group->name        = $validated['name'];
        $group->description = $validated['description'];
        $group->invite_only = $validated['invite_only'];

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
        $user->updateGroupStatus($group, Group::$STATUS_LEFT);

        //TODO: Add history entry
    }
}
