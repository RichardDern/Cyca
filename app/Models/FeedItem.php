<?php

namespace App\Models;

use App\Models\Traits\HasUrl;
use Illuminate\Database\Eloquent\Model;

class FeedItem extends Model
{
    use HasUrl;

    // -------------------------------------------------------------------------
    // ----| Properties |-------------------------------------------------------
    // -------------------------------------------------------------------------

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
        'ascii_url',
    ];

    // -------------------------------------------------------------------------
    // ----| Relations |--------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Feeds referenced by this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function feeds()
    {
        return $this->belongsToMany(Feed::class, 'feed_feed_items');
    }

    /**
     * Associated feed item state.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedItemStates()
    {
        return $this->hasMany(FeedItemState::class);
    }

    // -------------------------------------------------------------------------
    // ----| Scopes |-----------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Scope a query to only include feed items read by all users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllRead($query)
    {
        return $query->whereDoesntHave('feedItemStates', function ($subQuery) {
            $subQuery->where('is_read', false);
        });
    }

    /**
     * Scope a query to only include feed items read by all users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $date
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOlderThan($query, $date)
    {
        return $query->where('published_at', '<', $date)
            ->orWhereNull('published_at');
    }

    /**
     * Scope a query to only include feed items associated with specified feeds.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $feeds
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInFeeds($query, $feeds)
    {
        return $query->whereHas('feeds', function ($subQuery) use ($feeds) {
            $subQuery->whereIn('feeds.id', $feeds);
        });
    }

    /**
     * Scope a query to only include unread feed items for specified user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnreadFor($query, User $user)
    {
        return $query->whereHas('feedItemStates', function ($subQuery) use ($user) {
            $subQuery->where('user_id', $user->id)->where('is_read', false);
        });
    }

    /**
     * Scope a query to only include unread feed items for specified user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCountStates($query, User $user, bool $read = false)
    {
        return $query->withCount([
            'feedItemStates' => function ($subQuery) use ($user, $read) {
                $subQuery->where('user_id', $user->id)->where('is_read', $read);
            },
        ]);
    }

    // -------------------------------------------------------------------------
    // ----| Methods |----------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Return path to root folder for storing this document's assets. This path
     * can then be used to store and retrieve files using the Storage facade, so
     * it does not return the full path of a directory rather than the path
     * related to configured storage disk.
     *
     * @return string
     */
    public function getStoragePath()
    {
        if (empty($this->storagePath)) {
            $hash = $this->hash;

            $this->storagePath = 'public/feeditems/'.implode('/', str_split(($hash)));
        }

        return $this->storagePath;
    }
}
