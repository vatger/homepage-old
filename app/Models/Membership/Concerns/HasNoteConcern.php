<?php

namespace App\Models\Membership\Concerns;

use App\Models\Membership\Account\Note;

trait HasNoteConcern{

    public function hasNotes()
    {
        return Note::where('account_id', $this->id)->exists();
    }

    public function notes()
    {
        return $this->hasMany(Note::class)->orderBy('created_at', 'DESC');
    }

}
