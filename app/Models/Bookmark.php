<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Link between a folder (belonging to a user) and a document
 */
class Bookmark extends Pivot
{

    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Name of the table storing bookmarks
     *
     * @var string
     */
    public $table = 'bookmarks';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}
