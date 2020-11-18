<?php

namespace App\Models;

use App\Models\Traits\Folder\BuildsTree;
use App\Models\Traits\Folder\CreatesDefaultFolders;
use App\Models\Traits\HasHistory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use BuildsTree, CreatesDefaultFolders, HasHistory;

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
        'user_id',
        'group_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'icon',
        'iconColor',
        'is_selected',
        'is_expanded',
    ];

    /**
     * Attributes used to display this model in history
     *
     * @var array
     */
    protected $historyAttributes = [
        'title',
        'icon',
        'iconColor',
        'type',
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
        switch ($this->type) {
            // Unspecified type of folder
            default:
                return $this->attributes['title'];

            // Unread items
            case 'unread_items':
                return __('Unread items');

            // Root folder
            case 'root':
                return __('Root');
        }
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
        }
    }

    /**
     * Return icon's color as a CSS class
     *
     * @return string
     */
    public function getIconColorAttribute()
    {
        switch ($this->type) {
            // Unspecified type of folder
            default:
                return 'folder-common';

            // Unread items
            case 'unread_items':
                if ($this->feed_item_states_count > 0) {
                    return 'folder-unread-not-empty';
                }

                return 'folder-unread';

            // Root folder
            case 'root':
                return 'folder-root';
        }
    }

    /**
     * Return a formatted path to the folder, using every ascendant's title
     *
     * @return string
     */
    public function getBreadcrumbsAttribute()
    {
        $parts = [
            (string) view('partials.folder', ['folder' => $this]),
        ];

        $parent = $this->parent;

        while ($parent !== null) {
            $parts[] = (string) view('partials.folder', ['folder' => $parent]);
            $parent  = $parent->parent;
        }

        $parts[] = (string) view('partials.group', ['group' => $this->group]);

        return implode(' ', array_reverse($parts));
    }

    /**
     * Return a boolean value indicating if this folder is selected by current
     * user
     *
     * @return boolean
     */
    public function getIsSelectedAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->selectedFolder()->id === $this->id;
    }

    /**
     * Return a boolean value indicating if folder is expanded for specified
     * user
     *
     * @return boolean
     */
    public function getIsExpandedAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->getFolderExpandedState($this);
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
     * Creator of this folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Group this folder belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
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
    public function feedItemStates()
    {
        return $this->hasManyThrough(FeedItemState::class, Bookmark::class, 'folder_id', 'document_id', 'id', 'document_id');
    }

    /**
     * Folder's permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
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

    public static function listDocumentIds($folders, $group)
    {
        $unreadItemsFolder = $group->folders()->ofType('unread_items')->first();

        $query = $group->folders()->with('documents:document_id');

        if (!in_array($unreadItemsFolder->id, $folders)) {
            $query = $query->whereIn('id', $folders);
        }
        
        return $query->get()->pluck('documents')->flatten()->pluck('document_id')->unique();
    }

    /**
     * Return a list of ids of documents present in this folder
     *
     * @return array
     */
    public function getDocumentIds()
    {
        return $this->documents()->select('documents.id')->pluck('documents.id');
    }

    /**
     * Return a list of documents built for front-end for specified user
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Support\Collection
     */
    public function listDocuments(User $user)
    {
        $columns = [
            'documents.id',
            'documents.url',
            'documents.http_status_code',
            'documents.mimetype',
            'documents.title',
            'documents.favicon_path',
        ];

        if ($this->type === 'unread_items') {
            $documents = $this->group->documents()->pluck('document_id');

            return Document::select($columns)->with('feeds:feeds.id', 'feeds.ignored', 'bookmark')->withCount(['feedItemStates' => function ($query) use ($user) {
                $query->where('is_read', false)->where('user_id', $user->id);
            }])->whereHas('feedItemStates', function ($query) use ($user) {
                $query->where('is_read', false)->where('user_id', $user->id);
            })->whereIn('documents.id', $documents)
                ->get();
        } else {
            $documentIds = $this->getDocumentIds();

            return Document::select($columns)->with('feeds:feeds.id', 'feeds.ignored', 'bookmark')->withCount(['feedItemStates' => function ($query) use ($user) {
                $query->where('is_read', false)->where('user_id', $user->id);
            }])->whereIn('documents.id', $documentIds)
                ->get();
        }
    }

    /**
     * Return folder's permissions that applies to any user without explicit
     * permissions
     *
     * @return array
     */
    public function getDefaultPermissions()
    {
        $defaultPermissions = $this->permissions()->whereNull('user_id')->first();

        if (empty($defaultPermissions)) {
            return [
                'can_create_folder'       => false,
                'can_update_folder'       => false,
                'can_delete_folder'       => false,
                'can_create_document'     => false,
                'can_delete_document'     => false,
            ];
        }

        return [
            'can_create_folder'       => $defaultPermissions->can_create_folder,
            'can_update_folder'       => $defaultPermissions->can_update_folder,
            'can_delete_folder'       => $defaultPermissions->can_delete_folder,
            'can_create_document'     => $defaultPermissions->can_create_document,
            'can_delete_document'     => $defaultPermissions->can_delete_document,
        ];
    }

    /**
     * Return user's permission specific to this folder
     *
     * @return array
     */
    public function getUserPermissions(User $user = null)
    {
        if (empty($user)) {
            if (!auth()->check()) {
                return null;
            }

            $user = auth()->user();
        }

        return [
            'can_change_permissions'  => $this->group->user_id === $user->id,
            'can_create_folder'       => $user->can('createIn', $this),
            'can_update_folder'       => $user->can('update', $this),
            'can_delete_folder'       => $user->can('delete', $this),
            'can_create_document'     => $user->can('createBookmarkIn', $this),
            'can_delete_document'     => $user->can('deleteBookmarkFrom', $this),
        ];
    }

    public function setDefaultPermission($ability, $granted)
    {
        $defaultPermissions = $this->permissions()->whereNull('user_id')->first();

        if (empty($defaultPermissions)) {
            $this->permissions()->save(
                new Permission([
                    $ability => $granted
                ])
            );
        } else {
            $defaultPermissions->$ability = $granted;

            $defaultPermissions->save();
        }
    }
}
