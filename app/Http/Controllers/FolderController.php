<?php

namespace App\Http\Controllers;

use App\Http\Requests\Folders\SetPermissionsRequest;
use App\Http\Requests\Folders\StoreRequest;
use App\Http\Requests\Folders\UpdateRequest;
use App\Models\Folder;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Folder::class, 'folder');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return $user->getFlatTree();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Folder\StoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $validated    = $request->validated();
        $user         = $request->user();
        $parentFolder = Folder::find($validated['parent_id']);
        $group        = Group::find($validated['group_id']);

        $user->createdFolders()->save(new Folder([
            'title'     => $validated['title'],
            'parent_id' => $parentFolder->id,
            'group_id'  => $group->id,
        ]));

        $user->setFolderExpandedState(true, $parentFolder);

        return $user->getFlatTree($group);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Folder $folder)
    {
        $user = $request->user();

        $user->setSelectedFolder($folder);

        return $folder->listDocuments($user);
    }

    /**
     * Load every details of specified folder.
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request, Folder $folder)
    {
        $user = $request->user();

        if (!$user->can('view', $folder)) {
            abort(404);
        }

        if ($folder->type === 'unread_items') {
            $folder->feed_item_states_count = $folder->group->feedItemStatesCount;
        } else {
            $folder->user_permissions    = $folder->getUserPermissions($user);
            $folder->default_permissions = $folder->getDefaultPermissions();

            $folder->loadCount(['feedItemStates' => function ($query) use ($user) {
                $query->where('is_read', false)->where('user_id', $user->id);
            }]);

            $folder->group->loadCount('activeUsers');
        }

        return $folder;
    }

    /**
     * Load per-user permissions for specified folder.
     *
     * @return \Illuminate\Http\Response
     */
    public function perUserPermissions(Request $request, Folder $folder)
    {
        if (!$request->user()->can('setPermission', $folder)) {
            abort(404);
        }

        $users = $folder->group->activeUsers()->whereNotIn('users.id', [$request->user()->id])
            ->whereHas('permissions', function ($query) use ($folder) {
                $query->where('folder_id', $folder->id);
            })
            ->with(['permissions'=> function ($query) use ($folder) {
                $query->where('folder_id', $folder->id);
            }])
            ->select(['users.id', 'users.name', 'users.email'])
            ->get();

        return $users;
    }

    /**
     * Load list of users with no expicit permissions for specified folder.
     *
     * @return \Illuminate\Http\Response
     */
    public function usersWithoutPermissions(Request $request, Folder $folder)
    {
        if (!$request->user()->can('setPermission', $folder)) {
            abort(404);
        }

        $users = $folder->group->activeUsers()->whereNotIn('users.id', [$request->user()->id])
            ->whereDoesntHave('permissions', function ($query) use ($folder) {
                $query->where('folder_id', $folder->id);
            })
            ->select(['users.id', 'users.name', 'users.email'])
            ->get();

        return $users;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Http\Requests\Folder\UpdateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Folder $folder)
    {
        $validated = $request->validated();
        $user      = $request->user();

        if ($request->has('is_expanded')) {
            $user->setFolderExpandedState($validated['is_expanded'], $folder);
        }

        $folder->title     = $validated['title'];
        $folder->parent_id = $validated['parent_id'];

        if ($folder->isDirty()) {
            $folder->save();
        }

        if (!empty($folder->parent_id)) {
            $user->setFolderExpandedState(true, $folder->parent);
        }

        return $folder;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Folder $folder)
    {
        $user = $request->user();

        $user->setSelectedFolder(null, $folder->group);

        $folder->delete();

        return $user->getFlatTree();
    }

    /**
     * Toggle expanded/collapsed a whole folder's branch.
     *
     * @return \Illuminate\Http\Response
     */
    public function toggleBranch(Request $request, Folder $folder)
    {
        $user = $request->user();

        $user->setFolderExpandedState(!$folder->is_expanded, $folder, $folder->group, true);

        return $user->getFlatTree();
    }

    /**
     * Set permissions for specified folder, optionally for specified user.
     *
     * @param App\Http\Requests\Folder\SetPermissionsRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function setPermission(SetPermissionsRequest $request, Folder $folder)
    {
        if (!$request->user()->can('setPermission', $folder)) {
            abort(404);
        }

        $validated = $request->validated();

        $ability = !empty($validated['ability']) ? $validated['ability'] : null;
        $granted = !empty($validated['granted']) ? $validated['granted'] : false;

        if (empty($validated['user_id'])) {
            $folder->setDefaultPermission($ability, $granted);

            return $this->details($request, $folder);
        }

        $user = $folder->group->activeUsers()->findOrFail($validated['user_id']);

        $user->setFolderPermissions($folder, $ability, $granted);

        return $this->perUserPermissions($request, $folder);
    }

    /**
     * Remove permissions for specified user in specified folder.
     *
     * @param App\Http\Requests\Folder\SetPermissionsRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function removePermissions(Request $request, Folder $folder, User $user)
    {
        if (!$request->user()->can('setPermission', $folder)) {
            abort(404);
        }

        $user->permissions()->where('folder_id', $folder->id)->delete();

        return $this->perUserPermissions($request, $folder);
    }
}
