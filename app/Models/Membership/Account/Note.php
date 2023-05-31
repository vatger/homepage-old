<?php

namespace App\Models\Membership\Account;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Note extends Model
{

    use LogsActivity;

    protected $table = "membership_notes";

    protected $fillable = [
        'note',
        'account_id',
        'author_id',
    ];

    /**
     * the attributes to be logged
     * @var array
     */
    protected static $logAttributes = ['*'];

    /**
     * The user the note is about
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Membership\Account::class);
    }

    /**
     * The author of the note
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(\App\Models\Membership\Account::class, 'author_id', 'id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName == "created") {
            return "Eine Notiz über den Nutzer mit der vID {$this->account_id} wurde hinterlegt!";
        }
        if($eventName == "updated") {
            return "Eine Notiz über den Nutzer mit der vID {$this->account_id} wurde geändert!";
        }
        if($eventName == "deleted") {
            return "Eine Notiz über den Nutzer mit der vID {$this->account_id} wurde gelöscht!";
        }
    }

}
