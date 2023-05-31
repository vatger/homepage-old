<?php

namespace App\Models\TeamSpeak;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Registration extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'teamspeak_registration';

    protected $primaryKey = 'id';

    protected $fillable = ['*'];

    protected $attributes = ['registration_ip' => '0.0.0.0', 'last_ip' => '0.0.0.0'];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * Delete a registration while cascading the confirmation.
     *
     * @return [type] [description]
     */
    public function delete()
    {
        if ($this->confirmation) {
            $this->confirmation->delete();
        }
        parent::delete();
    }

    /**
     * The confirmation to this registration.
     *
     * @return [type] [description]
     */
    public function confirmation()
    {
        return $this->hasOne(\App\Models\TeamSpeak\Confirmation::class, 'registration_id', 'id');
    }

    /**
     * The associated account.
     *
     * @return [type] [description]
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Membership\Account::class, 'account_id');
    }
}
