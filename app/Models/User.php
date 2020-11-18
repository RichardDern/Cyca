<?php

namespace App\Models;

use App\Models\Traits\HasHistory;
use App\Models\Traits\User\HasFeeds;
use App\Models\Traits\User\HasFolders;
use App\Models\Traits\User\HasGroups;
use App\Services\Importer;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    use Notifiable, HasHistory, HasGroups, HasFolders, HasFeeds;

    # --------------------------------------------------------------------------
    # ----| Properties |--------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Attributes used to display this model in history
     *
     * @var array
     */
    protected $historyAttributes = [
        'name',
        'email',
    ];

    # --------------------------------------------------------------------------
    # ----| Attributes |--------------------------------------------------------
    # --------------------------------------------------------------------------

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Documents added to user's collection
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function documents()
    {
        return $this->hasManyThrough(Bookmark::class, Folder::class);
    }

    /**
     * Highlights registered by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function highlights()
    {
        return $this->hasMany(Highlight::class);
    }

    /**
     * Associated history entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userHistoryEntries()
    {
        return $this->hasMany(HistoryEntry::class);
    }

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        return $this->lang;
    }

    /**
     * Import initial set of data
     */
    public function importInitialData(Group $group)
    {
        $importer = new Importer();
        $importer->forUser($this)->inGroup($group)->fromFile(resource_path('initial_data.json'))->import();
    }
}
