<?php

namespace App\Models\Traits;

use App\Models\HistoryEntry;
use App\Models\User;
use Str;

/**
 * Classes using this trait can have a history
 */
trait HasHistory
{
    # --------------------------------------------------------------------------
    # ----| Relations |---------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Associated history entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function historyEntries() {
        return $this->morphMany(HistoryEntry::class, 'morphable');
    }

    # --------------------------------------------------------------------------
    # ----| Methods |-----------------------------------------------------------
    # --------------------------------------------------------------------------

    /**
     * Add a history entry for this model.
     * 
     * @param string $event Event name
     * @param null|array $properties Properties to pass to views
     * @param integer|\App\Models\User|array You could provide an integer (which 
     * will be resolved to corresponding user) or an array of users.
     */
    public function addHistoryEntry($event, $properties = null, $users) {
        if(\is_numeric($users) || get_class($users) === User::class) {
            $users = [$users];
        }

        foreach($users as $user) {
            $this->addHistoryEntryFor($event, $properties, $user);
        }
    }

    /**
     * Add a history entry for this model and specified user (which can be null)
     * 
     * @param string $event Event name
     * @param null|array $properties Properties to pass to views
     * @param integer|\App\Models\User|null
     */
    private function addHistoryEntryFor($event, $properties = null, $user = null) {
        $historyEntry = new HistoryEntry();

        $historyEntry->event = $event;

        // Associating user, if needed
        if(!empty($user)) {
            if(\is_numeric($user)) {
                $user = User::find($user);
            }
        
            $historyEntry->user()->associate($user);
        }

        $historyEntry->morphable()->associate($this);

        $historyEntry->properties = $properties;

        $historyEntry->save();
    }

    /**
     * Return an array representing this model, specific to history displaying
     * 
     * @return array
     */
    public function toHistoryArray() {
        $data = [];

        if(!empty($this->historyAttributes)) {
            foreach($this->historyAttributes as $attribute) {
                $data[$attribute] = $this->$attribute;
            }
        } else {
            $data = $this->toArray();
        }

        return $data;
    }
}