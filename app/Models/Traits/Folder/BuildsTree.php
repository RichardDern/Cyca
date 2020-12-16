<?php

namespace App\Models\Traits\Folder;

use App\Models\Group;
use App\Models\User;
use Arr;

/**
 * Constructs tree representation.
 */
trait BuildsTree
{
    /**
     * Return user's folders as a flat tree.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getFlatTreeFor(User $user, Group $group)
    {
        $tree  = [];
        $query = $group->folders()
            ->select([
                'id',
                'parent_id',
                'type',
                'title',
                'position',
                'group_id',
            ])
            ->withCount(['children', 'feedItemStates' => function ($query) use ($user) {
                $query->where('is_read', false)->where('user_id', $user->id);
            }])
            ->orderBy('parent_id', 'asc')->orderBy('position', 'asc')
            ->orderBy('title', 'asc');

        $folders = $query->get();

        $roots = $folders->collect()->filter(function ($folder) {
            return $folder->parent_id === null;
        });

        foreach ($roots as $root) {
            if ($root->type === 'unread_items') {
                $root->feed_item_states_count = $group->feedItemStatesCount;
            }

            $branch = self::buildBranch($root, $folders, 0);
            $tree[] = $branch;
        }

        return collect(Arr::flatten($tree));
    }

    /**
     * Construct a flat array of sub-folders for specified parent folder.
     *
     * @param \App\Models\Folder             $folder     Parent folder for the branch
     * @param \Illuminate\Support\Collection $allFolders All folders associated to the same user as the parent folder
     * @param int                            $depth      Current depth
     *
     * @return array
     */
    public static function buildBranch(self $folder, $allFolders, $depth)
    {
        $folder->depth = $depth;
        $branch        = [];

        $branch[] = $folder;

        $subFolders = $allFolders->collect()->where('parent_id', $folder->id);

        foreach ($subFolders as $subFolder) {
            $branch[] = self::buildBranch($subFolder, $allFolders, $depth + 1);
        }

        return $branch;
    }
}
