<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\FeedItem;
use Illuminate\Http\Request;

class FeedItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $feedIds = $request->input('feeds', []);

        if (empty($feedIds)) {
            return [];
        }

        $user   = $request->user();
        $folder = $user->selectedFolder();

        $queryBuilder = FeedItem::with('feeds:feeds.id,title', 'feeds.documents:documents.id')->inFeeds($feedIds);

        $queryBuilder->select([
            'feed_items.id',
            'url',
            'title',
            'published_at',
            'created_at',
            'updated_at',
        ]);

        if ($folder->type === 'unread_items') {
            $queryBuilder->unreadFor($user);
        }

        return $queryBuilder->countStates($user)->orderBy('published_at', 'desc')->simplePaginate(15);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, FeedItem $feedItem)
    {
        $feedItem->loadCount(['feedItemStates' => function ($query) use ($request) {
            $query->where('is_read', false)->where('user_id', $request->user()->id);
        }]);

        $feedItem->loadMissing('feeds');

        return $feedItem;
    }

    /**
     * Mark feed items as read.
     */
    public function markAsRead(Request $request)
    {
        $user = $request->user();

        if ($request->has('folders')) {
            return $user->markFeedItemsReadInFolders($request->input('folders'));
        }
        if ($request->has('documents')) {
            return $user->markFeedItemsReadInDocuments($request->input('documents'));
        }
        if ($request->has('feeds')) {
            return $user->markFeedItemsReadInFeeds($request->input('feeds'));
        }
        if ($request->has('feed_items')) {
            return $user->markFeedItemsRead($request->input('feed_items'));
        }
    }
}
