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
}
