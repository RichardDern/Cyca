<?php

namespace App\Models\Traits\Folder;

use App\Models\User;
use App\Models\FeedItemState;
use Arr;

/**
 * Constructs tree representation
 */
trait BuildsTree
{
    /**
     * Return user's folders as a flat tree.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getFlatTreeFor(User $user)
    {
        $tree  = [];
        $query = $user->folders()->withCount(['children', 'unreadFeedItems'])->orderBy('parent_id', 'asc')->orderBy('position', 'asc')->orderBy('title', 'asc');
        $folders = $query->get();

        $roots = $folders->collect()->filter(function ($folder) {
            return $folder->parent_id === null;
        });

        foreach ($roots as $root) {
            if($root->type === 'unread_items') {
                $root->unread_feed_items_count = FeedItemState::where('user_id', $root->user_id)->where('is_read', false)->count();
            }

            $branch = self::buildBranch($root, $folders, 0);
            $tree[] = $branch;
        }

        return collect(Arr::flatten($tree));
    }

    /**
     * Construct a flat array of sub-folders for specified parent folder
     *
     * @param \App\Models\Folder $folder Parent folder for the branch
     * @param \Illuminate\Support\Collection $allFolders All folders associated to the same user as the parent folder
     * @param integer $depth Current depth
     * @return array
     */
    private static function buildBranch(self $folder, $allFolders, $depth)
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
