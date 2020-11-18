<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedItemState extends Model
{

    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'user_id',
        'folder_id',
        'document_id',
        'feed_id',
        'feed_item_id',
    ];

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Associated groups
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function groups()
    {
        return $this->hasManyThrough(Group::class, Folder::class);
    }

    /**
     * Associated document
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    # --------------------------------------------------------------------------
    # ----| Scopes |------------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Scope a query to only include unread items
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
