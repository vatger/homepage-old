<?php

namespace App\Models\Membership;

use App\Models\Membership\Concerns\HasBanConcern;
use App\Models\Membership\Concerns\HasDataConcern;
use App\Models\Membership\Concerns\HasNoteConcern;
use App\Models\Membership\Concerns\HasRegionalgroupConcern;
use App\Models\Membership\Concerns\HasSettingConcern;
use App\Models\Membership\Concerns\HasTeamSpeakConcern;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Junges\ACL\Traits\UsersTrait;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class Account extends Authenticatable
{
    /*
     * An account is notifiable and also shall just be soft deleted
     */
    use Notifiable;
    use SoftDeletes;
    /*
     * An account has Settings, CERT-Data, Chat access, Roles and TeamSpeak
     */
    use HasSettingConcern;
    use HasDataConcern;
    use HasNoteConcern;
    use HasBanConcern;
    use HasRegionalgroupConcern;
    use HasTeamSpeakConcern;
    use UsersTrait;
    use Impersonate;

    use LogsActivity; // Indicates the logs activity trait is used
    use CausesActivity; // Grants access to the actions attribute. This returns all logged activity by the user

    /**
     * The name of the database table to be used.
     *
     * @var string
     */
    protected $table = 'membership_accounts';

    /**
     * Array of fieldnames that can be mass asigned.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'firstname', 'lastname', 'email', 'password', 'api_token',
    ];

    /**
     * These attributes are hidden from array/json representation.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token','access_token','refresh_token','token_expires',
    ];

    protected static $recordEvents = ['created', 'deleted'];

    /**
     * Attributes to be logged by the activity logger
     * @var array
     */
    protected static $logAttributes = ['*'];

    /**
     * Generates a new api access token for
     * this account.
     *
     * @return [type] [<description>]
     */
    public function renewApiToken()
    {
        $this->api_token = Str::random(80);
        $this->save();
    }

    /**
     * Concatinate the first- and lastname of an account.
     *
     * @return string
     */
    public function getUsernameAttribute()
    {
        return $this->firstname.' '.$this->lastname;
    }

    // since this doesn't work in jobs
    public function getIsCurrentlyForumBannedAttribute()
    {
        $now = Carbon::now()->utc();
        return $this->bans()->where(function($query) use ($now) {
            $query->where('permanent', true)->orWhere('banned_till', '>=', $now);
        })->where('forum', 1)->exists();
    }

    /**
     * Is this user allowed to impersonate others
     * @return boolean
     */
    public function canImpersonate()
    {
        return $this->hasPermission('administration.impersonate');
    }

    /**
     * Returns a string representing a link to VATSIM Stats.
     *
     * @return string
     */
    public function getStatsLinkAttribute()
    {
        return 'https://stats.vatsim.net/search_id.php?id='.$this->id;
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName == "created") {
            return "Erstmalige Anmeldung auf der Website von vACC Germany!";
        }
        if($eventName == "updated") {
            return "Die Nutzerdaten wurden aktualisiert!";
        }
        if($eventName == "deleted") {
            return "Der Nutzer wurde gelöscht!";
        }
    }
}
