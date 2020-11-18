<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUrl;

class FeedItem extends Model
{
    use HasUrl;
    
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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'ascii_url'
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
     * Associated feed item state
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedItemStates()
    {
        return $this->hasMany(FeedItemState::class);
    }
}
