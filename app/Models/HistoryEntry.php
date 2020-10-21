<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryEntry extends Model
{
    use HasFactory;

    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'folder_id', 'document_id', 'feed_id', 'event', 'details'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'date',
        'time',
        'text'
    ];

    # --------------------------------------------------------------------------
    # ----| Attributes |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Return entry's date
     * 
     * @return string
     */
    public function getDateAttribute() {
        return $this->created_at->format('Y-m-d');
    }

    /**
     * Return entry's time
     * 
     * @return string
     */
    public function getTimeAttribute() {
        return $this->created_at->format("H:i");
    }

    /**
     * Return a descriptive representation of this entry
     * 
     * @return string
     */
    public function getTextAttribute() {
        $folder = null;
        $parent = null;
        $document = null;
        $feed = null;

        $userEmail = $this->user ? $this->user->email : $this->details['user']['email'];
        $userName = $this->user ? $this->user->name : $this->details['user']['name'];

        $user = sprintf('<a href="mailto:%s">%s</a>', $userEmail, $userName);

        if($this->folder) {
            $iconColor = $this->folder->parent ? $this->folder->parent->iconColor : $this->folder->iconColor;
            $title = $this->folder->parent ? $this->folder->parent->breadcrumbs : $this->folder->title;

            $folder = sprintf('<span class="inline-folder %s">%s</span>',
                $iconColor,
                $title
            );
        } else if(!empty($this->details['folder'])) {
            $folder = sprintf('<span class="inline-folder folder-common">%s</span>', $this->details['folder']['breadcrumbs']);
        }

        $documentUrl = null;
        $documentText = null;

        if($this->document) {
            $documentUrl = $this->document->url;
            $documentText = sprintf("%s%s", sprintf('<img src="%s" class="favicon" />', $this->document->favicon), $this->document->title);
        } else if(!empty($this->details['document'])) {
            $documentUrl = $this->details['document']['url'];
            $documentText = $documentUrl;
        }

        if($documentUrl && $documentText) {
            $document = sprintf('<a class="inline-document" target="_blank" rel="noopener noreferrer" href="%s">%s</a>', 
                $documentUrl, 
                $documentText
            );
        }

        $feedUrl = null;
        $feedTitle = null;

        if($this->feed) {
            $feedUrl = $this->feed->url;
            $feedTitle = sprintf('%s%s', 
                sprintf('<img src="%s" class="favicon" />', $this->feed->favicon),
                $this->feed->title
            );
        } else if(!empty($this->details['feed'])) {
            $feedUrl = $this->details['feed']['url'];
            $feedTitle = $feedUrl;
        }

        if($feedUrl && $feedTitle) {
            $feed = sprintf('<a class="inline-document" target="_blank" rel="noopener noreferrer" href="%s">%s</a>', 
                $feedUrl, 
                $feedTitle
            );
        }

        switch($this->event) {
            default:
                return $this->event;
            case "user_created":
                return __("User :user created (:email)", [
                    'user' => $user, 
                    'email' => $userEmail
                ]);
            case "folder_created":
                if($parent) {
                    return __(":user created the folder :folder in :parent", [
                        'folder' => $folder, 
                        'parent' => $parent, 
                        'user' => $user, 
                    ]);
                } else {
                    return __(":user created the folder :folder", [
                        'folder' => $folder, 
                        'user' => $user, 
                    ]);
                }
            case "folder_deleted":
                if($parent) {
                    return __(":user deleted folder :folder from :parent", [
                        'folder' => $folder, 
                        'parent' => $parent, 
                        'user' => $user, 
                    ]);
                } else {
                    return __(":user deleted folder :folder", [
                        'folder' => $folder, 
                        'user' => $user, 
                    ]);
                }
            case "bookmark_created":
                return __(":user created a bookmark to :document in :folder", [
                    'folder' => $folder, 
                    'document' => $document,
                    'user' => $user, 
                ]);
            case "bookmark_deleted":
                return __(":user removed the bookmark to :document from :folder", [
                    'folder' => $folder, 
                    'document' => $document,
                    'user' => $user, 
                ]);
            case "feed_ignored":
                return __(":user ignores feed :feed", [
                    'feed' => $feed,
                    'user' => $user, 
                ]);
            case "feed_followed":
                return __(":user follows feed :feed", [
                    'feed' => $feed,
                    'user' => $user, 
                ]);
        }
    }

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Related user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Related folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Related document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Related feed
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }
}
