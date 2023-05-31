<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    
    protected $table = "events";

    protected $dates = [
        'begins_at',
        'ends_at',
        'created_at',
        'updated_at',
    ];
}
