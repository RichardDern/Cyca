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

        $user = $request->user();

        $queryBuilder = FeedItem::with('feeds:feeds.id,title', 'feeds.documents:documents.id')->whereHas('feeds', function ($query) use ($feedIds) {
            $query->whereIn('feeds.id', $feedIds);
        });

        $queryBuilder->select(['feed_items.id', 'url', 'title', 'published_at', 'created_at', 'updated_at']);

        $folder = $user->selectedFolder();

        if ($folder->type === 'unread_items') {
            $queryBuilder->whereHas('feedItemStates', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_read', false);
            });
        }

        return $queryBuilder->withCount(['feedItemStates' => function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_read', false);
        }])->orderBy('published_at', 'desc')->simplePaginate(15);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeedItem  $feedItem
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, FeedItem $feedItem)
    {
        $feedItem->loadCount(['feedItemStates' => function ($query) use ($request) {
            $query->where('is_read', false)->where('user_id', $request->user()->id);
        }]);

        return $feedItem;
    }

    /**
     * Mark feed items as read
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function markAsRead(Request $request)
    {
        $user = $request->user();

        if ($request->has('folders')) {
            return $user->markFeedItemsReadInFolders($request->input('folders'));
        } elseif ($request->has('documents')) {
            return $user->markFeedItemsReadInDocuments($request->input('documents'));
        } elseif ($request->has('feeds')) {
            return $user->markFeedItemsReadInFeeds($request->input('feeds'));
        } elseif ($request->has('feed_items')) {
            return $user->markFeedItemsRead($request->input('feed_items'));
        }
    }
}
