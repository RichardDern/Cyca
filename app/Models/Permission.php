<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
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
    public $fillable = [
        'can_create_folder',
        'can_update_folder',
        'can_delete_folder',
        'can_create_document',
        'can_delete_document'
    ];
 
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public $casts = [
        'can_create_folder'   => 'boolean',
        'can_update_folder'   => 'boolean',
        'can_delete_folder'   => 'boolean',
        'can_create_document' => 'boolean',
        'can_delete_document' => 'boolean',
    ];

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
}
