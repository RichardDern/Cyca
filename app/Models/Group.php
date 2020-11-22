<?php

namespace App\Models;

use App\Models\FeedItemState;
use App\Models\Traits\HasHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory, HasHistory;

    # --------------------------------------------------------------------------
    # ----| Constants |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Group is owned by related user
     */
    public static $STATUS_OWN = 'own';

    /**
     * Group was created by related user
     */
    public static $STATUS_CREATED = 'created';

    /**
     * User has been invited in the group
     */
    public static $STATUS_INVITED = 'invited';

    /**
     * User accepted to join the group
     */
    public static $STATUS_ACCEPTED = 'accepted';

    /**
     * User declined joining the group
     */
    public static $STATUS_REJECTED = 'rejected';

    /**
     * User asked to join a group
     */
    public static $STATUS_JOINING = 'joining';

    /**
     * User has left the group
     */
    public static $STATUS_LEFT = 'left';

    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'invite_only',
        'auto_accept_users'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'invite_only'                  => 'boolean',
        'auto_accept_users'            => 'boolean',
        'feed_item_states_count'       => 'integer',
    ];

    /**
     * Attributes used to display this model in history
     *
     * @var array
     */
    protected $historyAttributes = [
        'name',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_selected',
        'feed_item_states_count',
    ];

    protected $feedItemStatesCount = null;

    # --------------------------------------------------------------------------
    # ----| Attributes |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Return a boolean value indicating if this group is selected by current
     * user
     *
     * @return boolean
     */
    public function getIsSelectedAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->selectedGroup()->id === $this->id;
    }

    /**
     * Return the number of unread feed items for this group and current user
     *
     * @return integer
     */
    public function getFeedItemStatesCountAttribute()
    {
        if (!auth()->check()) {
            return 0;
        }

        if ($this->feedItemStatesCount !== null) {
            return $this->feedItemStatesCount;
        }

        return $this->getUnreadFeedItemsCountFor(auth()->user());
    }

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Creator of the group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Associated users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_groups')->withPivot(['status']);
    }

    /**
     * Associated users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function activeUsers()
    {
        return $this->belongsToMany(User::class, 'user_groups')->withPivot(['status'])->whereIn('status', [
            self::$STATUS_OWN,
            self::$STATUS_CREATED,
            self::$STATUS_ACCEPTED,
        ]);
    }

    /**
     * Associated users in a pending state (either invited or asking to join,
     * without an answer yet)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pendingUsers()
    {
        return $this->belongsToMany(User::class, 'user_groups')->withPivot(['status'])->whereIn('status', [
            self::$STATUS_INVITED,
            self::$STATUS_JOINING
        ]);
    }

    /**
     * Associated folders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    /**
     * Associated bookmarks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasManyThrough(Bookmark::class, Folder::class);
    }

    # --------------------------------------------------------------------------
    # ----| Scopes |------------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Scope a query to only include user's own group
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwn($query)
    {
        return $query->where('status', self::$STATUS_OWN);
    }

    /**
     * Scope a query to only include groups the user is active
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::$STATUS_OWN,
            self::$STATUS_CREATED,
            self::$STATUS_ACCEPTED,
        ]);
    }

    /**
     * Scope a query to only include visible groups
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('invite_only', false);
    }

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Create default folders for this group.
     *
     * @return void
     */
    public function createDefaultFolders()
    {
        Folder::createDefaultFoldersFor($this->creator, $this);
    }

    public function getUnreadFeedItemsCountFor(User $user)
    {
        $documentsIds = $this->documents()->pluck('document_id')->unique();

        $this->feedItemStatesCount = FeedItemState::whereIn('document_id', $documentsIds)
            ->where('is_read', false)->where('user_id', $user->id)->count();

        return $this->feedItemStatesCount;
    }
}
