<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\IgnoredFeed;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    /**
     * Ignore specified feed.
     *
     * @return \Illuminate\Http\Response
     */
    public function ignore(Request $request, Feed $feed)
    {
        $ignoredFeed = IgnoredFeed::where('user_id', $request->user()->id)->where('feed_id', $feed->id)->first();

        if (!$ignoredFeed) {
            $ignoredFeed = new IgnoredFeed();

            $ignoredFeed->user()->associate($request->user());
            $ignoredFeed->feed()->associate($feed);

            $ignoredFeed->save();
        }
    }

    /**
     * Follow specified feed.
     *
     * @return \Illuminate\Http\Response
     */
    public function follow(Request $request, Feed $feed)
    {
        $ignoredFeed = IgnoredFeed::where('user_id', $request->user()->id)->where('feed_id', $feed->id)->first();

        if ($ignoredFeed) {
            $ignoredFeed->delete();
        }
    }
}
