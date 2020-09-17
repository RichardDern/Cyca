<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedItem extends Model
{
    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'published_at',
    ];

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Feeds referenced by this item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function feeds()
    {
        return $this->belongsToMany(Feed::class, 'feed_feed_items');
    }

    /**
     * Associated unread feed items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unreadFeedItems() {
        return $this->hasMany(FeedItemState::class)->where('user_id', auth()->user()->id)->where('is_read', false);
    }
}
