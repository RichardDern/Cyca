<?php

namespace App\Models\Traits\User;

use App\Models\FeedItemState;
use App\Models\Folder;
use App\Models\Group;
use App\Models\Document;

trait HasFeeds
{
    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Associated feed item state
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedItemStates()
    {
        return $this->hasMany(FeedItemState::class);
    }

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Mark feed items as read in specified folders, as an array of folder ids
     *
     * @param array $folders
     */
    public function markFeedItemsReadInFolders($folders, Group $group = null)
    {
        if (empty($group)) {
            $group = $this->selectedGroup();
        }

        $unreadItemsFolder = $group->folders()->ofType('unread_items')->first();

        $query = $group->folders()->with('documents:documents.id');

        if (!in_array($unreadItemsFolder->id, $folders)) {
            $query = $query->whereIn('folders.id', $folders);
        }
        
        $folders     = $query->get();
        $documentIds = $query->get()->pluck('documents')->flatten()->pluck('id')->unique();
        
        $query       = $this->feedItemStates()->unread()->whereIn('document_id', $documentIds);
        $feedItemIds = $query->pluck('feed_item_id')->unique();
        
        $query->update(['is_read' => true]);

        return $this->countUnreadItems([
            'folders'            => $folders,
            'documents'          => $documentIds,
            'updated_feed_items' => $feedItemIds
        ]);
    }

    /**
     * Mark feed items as read in specified documents, as an array of document
     * ids
     *
     * @param array $documents
     */
    public function markFeedItemsReadInDocuments($documents)
    {
        $query       = $this->feedItemStates()->unread()->whereIn('document_id', $documents);
        $feedItemIds = $query->pluck('feed_item_id')->unique();
        
        $query->update(['is_read' => true]);

        return $this->countUnreadItems([
            'documents'          => $documents,
            'updated_feed_items' => $feedItemIds
        ]);
    }

    /**
     * Mark feed items as read in specified feeds, as an array of feed ids
     *
     * @param array $feeds
     */
    public function markFeedItemsReadInFeeds($feeds)
    {
        abort(404);
    }

    /**
     * Mark specified feed items as read, as an array of feed item ids
     *
     * @param array $feedItems
     */
    public function markFeedItemsRead($feedItems)
    {
        $query       = $this->feedItemStates()->unread()->whereIn('feed_item_id', $feedItems);
        $feedItemIds = $query->pluck('feed_item_id')->unique();
        $documentIds = $query->pluck('document_id')->unique();
        
        $query->update(['is_read' => true]);

        return $this->countUnreadItems([
            'documents'          => $documentIds,
            'updated_feed_items' => $feedItemIds
        ]);
    }

    /**
     * Calculate unread items counts for current user. The $for array allows to
     * be more specific by specifying ids for feed_items, documents or folder
     * to re-count for in particular.
     *
     * This method returns an array containing the id of each document and
     * folder along with corresponding unread items count, as well as a total
     * of unread items count for each group and folders of type "unread_items".
     *
     * @param array $for
     * @return array
     */
    public function countUnreadItems($for)
    {
        $data = [];

        if (empty($for['documents'])) {
            if (!empty($for['folders'])) {
                $for['documents'] = Folder::listDocumentIds(collect($for['folders'])->pluck('id')->all(), $this->selectedGroup());
            }
        }

        $countPerDocument = $this->feedItemStates()->unread()->whereIn('document_id', $for['documents'])->get()->countBy('document_id')->all();
        $countPerGroup    = [];

        if (!empty($for['documents'])) {
            foreach ($for['documents'] as $id) {
                if (!array_key_exists($id, $countPerDocument)) {
                    $countPerDocument[$id] = 0;
                }
            }

            if (empty($for['folders'])) {
                $folderIds = Document::with('folders')->find($for['documents'])->pluck('folders')->flatten()->pluck('id');
            
                $for['folders'] = Folder::find($folderIds);
            }
        }

        foreach ($for['folders'] as $folder) {
            $countPerFolder[$folder->id] = $this->feedItemStates()->unread()->whereIn('document_id', $folder->getDocumentIds())->count();
        }

        foreach ($this->groups as $group) {
            $totalUnreadItems    = $group->getUnreadFeedItemsCountFor($this);
            $unreadItemsFolderId = $group->folders()->ofType('unread_items')->first()->id;

            $countPerFolder[$unreadItemsFolderId] = $totalUnreadItems;
            $countPerGroup[$group->id]            = $totalUnreadItems;
        }

        $data = [
            'documents'          => $countPerDocument,
            'folders'            => $countPerFolder,
            'groups'             => $countPerGroup,
            'updated_feed_items' => !empty($for['updated_feed_items']) ? $for['updated_feed_items'] : null
        ];

        return $data;
    }
}
