<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use App\Services\Importer;
use App\Models\Traits\HasHistory;

class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    use Notifiable, HasHistory;

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
        'email'
    ];

    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Folders owned by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

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
    public function userHistoryEntries() {
        return $this->hasMany(HistoryEntry::class);
    }

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Create default folders for this user.
     *
     * @throws \App\Exceptions\UserDoesNotExistsException
     * @return void
     */
    public function createDefaultFolders()
    {
        if (empty($this->id)) {
            throw new \App\Exceptions\UserDoesNotExistsException("Cannot create default folders for inexisting user");
        }

        Folder::createDefaultFoldersFor($this);
    }

    /**
     * Return user's folders as a flat tree.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFlatTree()
    {
        return Folder::getFlatTreeFor($this);
    }

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
    public function importInitialData()
    {
        $importer = new Importer();
        $importer->forUser($this)->fromFile(resource_path('initial_data.json'))->import();
    }
}
