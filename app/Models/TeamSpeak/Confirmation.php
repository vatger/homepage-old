<?php

namespace App\Models\TeamSpeak;

use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    public $incrementing = false;

    protected $table = 'teamspeak_confirmation';

    protected $primaryKey = 'registration_id';

    public function registration()
    {
        return $this->belongsTo(\App\Models\Membership\TeamSpeak\Registration::class, 'registration_id', 'id');
    }
}
