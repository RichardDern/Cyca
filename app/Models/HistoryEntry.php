<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class HistoryEntry extends Model
{
    use HasFactory;

    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'array',
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
        $view = \sprintf('partials.history.%s.%s', Str::lower(class_basename($this->morphable_type)), $this->event);

        return (string) view($view, $this->properties);
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
     * Get the owning model.
     */
    public function morphable()
    {
        return $this->morphTo();
    }
}
