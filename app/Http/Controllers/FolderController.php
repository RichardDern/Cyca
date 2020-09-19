<?php

namespace App\Http\Controllers;

use App\Http\Requests\Folders\StoreRequest;
use App\Http\Requests\Folders\UpdateRequest;
use App\Models\Folder;
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
        return $request->user()->getFlatTree();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Folder\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $parentFolder = Folder::find($validated['parent_id']);

        if ($parentFolder->type !== 'folder' && $parentFolder->type !== 'root') {
            abort(422);
        }

        $request->user()->folders()->save(new Folder([
            'title'     => $validated['title'],
            'parent_id' => $validated['parent_id'],
        ]));

        return $this->index($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Folder $folder)
    {
        $request->user()->folders()->where('id', '<>', $folder->id)->update(['is_selected' => false]);

        $folder->is_selected = true;

        $folder->save();

        return $folder->listDocuments();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Folder $folder)
    {
        abort(404);
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

        // Don't move a folder to a descendant
        $parent = $request->user()->folders()->findOrFail($validated['parent_id']);

        while ($parent !== null) {
            if ($parent->id === $folder->id) {
                abort(422, "Cannot move a folder to one of its descendants");
            }

            $parent = $parent->parent;
        }

        $folder->title     = $validated['title'];
        $folder->parent_id = $validated['parent_id'];

        if ($request->has('is_expanded')) {
            $folder->is_expanded = $validated['is_expanded'];
        }

        $folder->save();

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
        $folder->delete();

        // We want to ensure at least the root folder is selected
        $request->user()->folders()->update(['is_selected' => false]);
        $request->user()->folders()->where('type', 'root')->update(['is_selected' => true]);

        return $this->index($request);
    }
}
