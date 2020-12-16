<?php

namespace App\Models;

use App\Models\Traits\Feed\AnalysesFeed;
use App\Models\Traits\HasUrl;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use AnalysesFeed;
    use HasUrl;

    // -------------------------------------------------------------------------
    // ----| Properties |-------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'favicon',
        'is_ignored',
        'ascii_url',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'checked_at',
    ];

    /**
     * Hash of URL.
     *
     * @var string
     */
    private $hash;

    /**
     * Path to storage.
     *
     * @var string
     */
    private $storagePath;

    // -------------------------------------------------------------------------
    // ----| Attributes |-------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Return feed's title, or url if empty.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        if (!empty($this->attributes['title'])) {
            return $this->attributes['title'];
        }

        return $this->url;
    }

    /**
     * Return full URL to favicon.
     *
     * @return string
     */
    public function getFaviconAttribute()
    {
        if (!empty($this->attributes['favicon_path'])) {
            return asset('storage/'.str_replace('public/', '', $this->attributes['favicon_path']));
        }

        $document = $this->documents()->first();

        if ($document) {
            return $document->favicon;
        }

        return null;
    }

    /**
     * Return a boolean value indicating if auth'ed user has ignored this feed.
     *
     * @return bool
     */
    public function getIsIgnoredAttribute()
    {
        if (auth()->user()) {
            return $this->ignored->firstWhere('user_id', auth()->user()->id) !== null;
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // ----| Relations |--------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Documents referenced by this feed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_feeds');
    }

    /**
     * Feed items referenced by this feed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function feedItems()
    {
        return $this->belongsToMany(FeedItem::class, 'feed_feed_items');
    }

    /**
     * Associated unread feed items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedItemStates()
    {
        return $this->hasMany(FeedItemState::class);
    }

    /**
     * Users ignoring this feed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ignored()
    {
        return $this->hasMany(IgnoredFeed::class);
    }

    // -------------------------------------------------------------------------
    // ----| Scopes |-----------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Scope a query to only include feeds than were updated before specifed
     * date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $date
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNeedingUpdate($query, $date)
    {
        return $query->where('checked_at', '<', $date)->orWhereNull('checked_at');
    }

    // -------------------------------------------------------------------------
    // ----| Methods |----------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Build a hash for document's URL. Used to build path for storing assets
     * related to this document. It doesn't need to provide a "secure" hash like
     * for a password, so we're just going to use md5.
     *
     * The purpose of this hash is multiple:
     *
     * - Maximum number of folders in each level is 16, and hierarchy is 32
     * folders deep, so it can be handled by any file system without problem
     * - As it is based on document's URL and date of creation in Cyca, files
     * cannot be "stolen" by direct access (Cyca couldn't and shouldn't be used
     * as a favicon repository used by everyone, for instance)
     * - It avoids issues with intl domain names or special chars in URLs
     * - On the other side, it would be easy for Cyca to quickly know where to
     * store assets for that particular document, and we can store all assets
     * related to that document in the same folder
     *
     * @return string
     */
    public function getHash()
    {
        if (empty($this->hash)) {
            $this->hash = md5($this->url.$this->created_at);
        }

        return $this->hash;
    }

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
            $hash = $this->getHash();

            $this->storagePath = 'public/feeds/'.implode('/', str_split(($hash)));
        }

        return $this->storagePath;
    }

    /**
     * Return a boolean value indicating if this feed still belongs to any
     * document.
     *
     * @return true
     */
    public function isOrphan()
    {
        return $this->documents()->count() === 0;
    }

    /**
     * Return a boolean value indicating if this feed was orphan for
     * specified days.
     *
     * @return true
     */
    public function wasOrphanFor(int $days)
    {
        return empty($this->checked_at) || $this->checked_at->addDays($days)->lt(now());
    }
}
