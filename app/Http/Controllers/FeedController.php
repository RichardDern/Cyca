<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Http\Request;
use App\Models\IgnoredFeed;

class FeedController extends Controller
{
    /**
     * Ignore specified feed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feed  $feed
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
     * Follow specified feed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feed  $feed
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
