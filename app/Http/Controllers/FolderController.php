<?php

namespace App\Http\Controllers;

use App\Http\Requests\Folders\StoreRequest;
use App\Http\Requests\Folders\UpdateRequest;
use App\Models\Folder;
use App\Models\Group;
use App\Http\Requests\Folders\SetPermissionsRequest;
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
     * @param  \App\Http\Requests\Folder\StoreRequest  $request
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
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Folder $folder)
    {
        $user = $request->user();

        $user->setSelectedFolder($folder);

        return $folder->listDocuments($user);
    }

    /**
     * Load every details for specified folder
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request, Folder $folder)
    {
        $user = $request->user();
        
        if (!$user->can('view', $folder)) {
            abort(404);
        }

        $folder->user_permissions    = $folder->getUserPermissions($user);
        $folder->default_permissions = $folder->getDefaultPermissions();
        
        return $folder;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Folder\UpdateRequest $request
     * @param  \App\Models\Folder  $folder
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

        //TODO: Send a "folder updated" notification to other users in the group

        return $folder;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Folder $folder)
    {
        $user = $request->user();

        $user->setSelectedFolder(null, $folder->group);

        $folder->delete();

        //TODO: Send a "folder deleted" notification to other users in the group

        return $user->getFlatTree();
    }

    /**
     * Toggle expanded/collapsed a whole folder's branch
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function toggleBranch(Request $request, Folder $folder)
    {
        $user = $request->user();

        $user->setFolderExpandedState(!$folder->is_expanded, $folder, $folder->group, true);

        return $user->getFlatTree();
    }

    public function setPermission(SetPermissionsRequest $request, Folder $folder)
    {
        $validated = $request->validated();

        $folder->setDefaultPermission($validated['ability'], $validated['granted']);

        return $this->details($request, $folder);
    }
}
