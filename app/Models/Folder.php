<?php

namespace App\Models;

use App\Models\Traits\Folder\BuildsTree;
use App\Models\Traits\Folder\CreatesDefaultFolders;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use BuildsTree, CreatesDefaultFolders;

    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'title',
        'parent_id',
        'position',
        'is_selected',
        'is_expanded',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'icon',
        'iconColor',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_expanded' => 'boolean',
        'is_selected' => 'boolean',
    ];

    # --------------------------------------------------------------------------
    # ----| Attributes |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Return folder's title.
     *
     * If it's a special folder, ie not created by user, we will automatically
     * translate its original title.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        if ($this->type !== 'folder') {
            return __($this->attributes['title']);
        }

        return $this->attributes['title'];
    }

    /**
     * Return folder's icon as a fragment identifier for a SVG sprite.
     *
     * @return string
     */
    public function getIconAttribute()
    {
        switch ($this->type) {
            // Unspecified type of folder
            default:
                return 'folder';

            // Unread items
            case 'unread_items':
                return 'unread_items';

            // Root folder
            case 'root':
                return 'house';

            // My account link
            case 'account':
                return 'account';

            // Log out link
            case 'logout':
                return 'logout';
        }
    }

    /**
     * Return icon's color
     *
     * @return string
     */
    public function getIconColorAttribute()
    {
        switch ($this->type) {
            // Unspecified type of folder
            default:
                return 'text-yellow-500';

            // Unread items
            case 'unread_items':
                if($this->unread_feed_items_count) {
                    return 'text-purple-500';
                }

                return 'text-gray-100';

            // Root folder
            case 'root':
                return 'text-blue-500';

            // My account link
            case 'account':
                return 'text-green-500';

            // Log out link
            case 'logout':
                return 'text-red-500';
        }
    }

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Parent folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Children folders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /**
     * Owner of this folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Documents in this folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'bookmarks')->using(Bookmark::class)->as('bookmark')->withPivot(['initial_url', 'created_at', 'updated_at', 'visits']);
    }

    /**
     * Associated unread feed items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unreadFeedItems()
    {
        return $this->hasMany(FeedItemState::class)->where('user_id', auth()->user()->id)->where('is_read', false);
    }

    # --------------------------------------------------------------------------
    # ----| Scopes |------------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Scope a query to only include folders of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Return a list of documents built for front-end
     */
    public function listDocuments()
    {
        $self = $this;

        if ($this->type === 'unread_items') {
            return Document::with(['feeds.ignored', 'bookmark'])->withCount(['unreadFeedItems' => function ($query) use ($self) {
                $query->where('is_read', false)->where('user_id', $self->user_id);
            }])->whereHas('unreadFeedItems', function ($query) use ($self) {
                $query->where('is_read', false)->where('user_id', $self->user_id);
            })->get();
        } else {
            return $this->documents()->withCount(['unreadFeedItems' => function ($query) use ($self) {
                $query->where('is_read', false)->where('user_id', $self->user_id);
            }])->with('feeds.ignored')->get();
        }
    }
}
