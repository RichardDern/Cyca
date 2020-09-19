<?php

namespace App\Http\Controllers;

use App\Models\FeedItem;
use App\Models\FeedItemState;
use Illuminate\Http\Request;
use App\Notifications\UnreadItemsChanged;
use Illuminate\Support\Facades\Notification;

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

        $queryBuilder = FeedItem::with('feeds')->whereHas('feeds', function ($query) use ($feedIds) {
            $query->whereIn('feeds.id', $feedIds);
        });

        $queryBuilder->select(['id', 'url', 'title', 'published_at', 'created_at', 'updated_at']);

        $folder = $request->user()->folders()->where('is_selected', true)->first();

        if ($folder->type === 'unread_items') {
            $queryBuilder->whereHas('unreadFeedItems', function ($query) {
                $query->where('feed_item_states.user_id', auth()->user()->id)->where('is_read', false);
            });
        }

        return $queryBuilder->withCount(['unreadFeedItems' => function ($query) {
            $query->where('is_read', false)->where('user_id', auth()->user()->id);
        }])->orderBy('published_at', 'desc')->simplePaginate(15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeedItem  $feedItem
     * @return \Illuminate\Http\Response
     */
    public function show(FeedItem $feedItem)
    {
        $feedItem->loadCount(['unreadFeedItems' => function ($query) {
            $query->where('is_read', false)->where('user_id', auth()->user()->id);
        }]);

        return $feedItem;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FeedItem  $feedItem
     * @return \Illuminate\Http\Response
     */
    public function edit(FeedItem $feedItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeedItem  $feedItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeedItem $feedItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FeedItem  $feedItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeedItem $feedItem)
    {
        //
    }

    /**
     * Mark feed items as read
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function markAsRead(Request $request)
    {
        if ($request->has('folders')) {
            $folder = $request->user()->folders()->find($request->input('folders')[0]);

            if ($folder && $folder->type === 'unread_items') {
                FeedItemState::where('user_id', $request->user()->id)->update(['is_read' => true]);
            } else {
                FeedItemState::where('user_id', $request->user()->id)->whereIn('folder_id', $request->input('folders'))->update(['is_read' => true]);
            }
        } else if ($request->has('documents')) {
            FeedItemState::where('user_id', $request->user()->id)->whereIn('document_id', $request->input('documents'))->update(['is_read' => true]);
        } else if ($request->has('feeds')) {
            FeedItemState::where('user_id', $request->user()->id)->whereIn('feed_id', $request->input('feeds'))->update(['is_read' => true]);
        } else if ($request->has('feed_items')) {
            FeedItemState::where('user_id', $request->user()->id)->whereIn('feed_item_id', $request->input('feed_items'))->update(['is_read' => true]);
        }
    }
}
