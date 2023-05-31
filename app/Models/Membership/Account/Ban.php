<?php

namespace App\Models\Membership\Account;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Ban extends Model
{

	use LogsActivity;

	protected $table = 'membership_bans';

	protected $fillable = [
        'reason',
        'permanent',
        'banned_till',
        'account_id',
        'author_id',
        'teamspeak',
        'homepage',
        'forum',
    ];

    protected $dates = [
    	'banned_till',
    ];

    /**
     * The attributes to be logged here
     * @var array
     */
    protected static $logAttributes = ['*'];

    /**
     * The user the ban is for
     * @return [type] [description]
     */
	public function account()
	{
		return $this->belongsTo(\App\Models\Membership\Account::class);
	}

	/**
	 * The author of the ban
	 * @return [type] [description]
	 */
	public function author()
	{
		return $this->belongsTo(\App\Models\Membership\Account::class, 'author_id', 'id');
	}

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName == "created") {
            return "Der Nutzer mit der vID {$this->account_id} wurde gesperrt!";
        }
        if($eventName == "updated") {
            return "Die Sperre des Nutzer mit der vID {$this->account_id} wurde geändert!";
        }
        if($eventName == "deleted") {
            return "Der Nutzer mit der vID {$this->account_id} wurde entsperrt!";
        }
    }

}
