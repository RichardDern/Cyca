<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Document\AnalysesDocument;
use App\Models\Traits\HasUrl;

class Document extends Model
{
    use AnalysesDocument, HasUrl;

    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
    ];

    /**
     * Array of folders containing this document. User will be specified in the
     * findDupplicatesFor method.
     */
    protected $dupplicates = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'dupplicates',
        'favicon',
        'ascii_url'
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
     * Hash of URL
     *
     * @var string
     */
    private $hash = null;

    /**
     * Path to storage
     *
     * @var string
     */
    private $storagePath = null;

    # --------------------------------------------------------------------------
    # ----| Attributes |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Return document's title, or url if empty
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
     * Return array of folders containing a bookmark to this document
     *
     * @return array
     */
    public function getDupplicatesAttribute()
    {
        return $this->dupplicates;
    }

    /**
     * Return full URL to favicon
     *
     * @return string
     */
    public function getFaviconAttribute()
    {
        if (empty($this->attributes['favicon_path'])) {
            return null;
        }

        return asset('storage/' . str_replace('public/', '', $this->attributes['favicon_path']));
    }

    public function getHttpStatusTextAttribute() {
        if (!empty($this->attributes['http_status_text'])) {
            return $this->attributes['http_status_text'];
        }

        if(empty($this->http_status_code)) {
            if(empty($this->checked_at)) {
                return __("Cyca did not check this document yet");
            }

            return __("Cyca could not reach this document URL");
        }
    }

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Bookmarks referencing this document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bookmark()
    {
        return $this->belongsToMany(Document::class, 'bookmarks')->using(Bookmark::class)->as('bookmark')->withPivot(['initial_url', 'created_at', 'updated_at', 'visits']);
    }

    /**
     * Folders referencing this document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function folders()
    {
        return $this->belongsToMany(Folder::class, 'bookmarks');
    }

    /**
     * Feeds referenced by this document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function feeds()
    {
        return $this->belongsToMany(Feed::class, 'document_feeds');
    }

    /**
     * Associated feed items states
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedItemStates()
    {
        return $this->hasMany(FeedItemState::class);
    }

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Find dupplicates of this document in specified user's folders
     */
    public function findDupplicatesFor(User $user)
    {
        $ids = $user->documents()->where('document_id', $this->id)->select('folder_id')->pluck('folder_id');

        $folders = Folder::find($ids);

        foreach ($folders as $folder) {
            $this->dupplicates[] = [
                'id'          => $folder->id,
                'group_id'    => $folder->group->id,
                'breadcrumbs' => $folder->breadcrumbs
            ];
        }

        return $this->dupplicates;
    }

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
            $this->hash = md5($this->url . $this->created_at);
        }

        return $this->hash;
    }

    /**
     * Return path to root folder for storing this document's assets. This path
     * can then be used to store and retrieve files using the Storage facade, so
     * it does not return the full path of a directory rather than the path
     * related to configured storage disk
     *
     * @return string
     */
    public function getStoragePath()
    {
        if (empty($this->storagePath)) {
            $hash = $this->getHash();

            $this->storagePath = 'public/documents/' . implode('/', str_split(($hash)));
        }

        return $this->storagePath;
    }
}
