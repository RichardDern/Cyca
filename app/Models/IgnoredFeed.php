<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgnoredFeed extends Model
{
    public $timestamps = false;

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Ignored feed
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }

    /**
     * User ignoring feed
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
