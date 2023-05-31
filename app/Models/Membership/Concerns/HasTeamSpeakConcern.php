<?php

namespace App\Models\Membership\Concerns;

trait HasTeamSpeakConcern
{
    /**
     * All registrations a user has.
     *
     * @return [type] [description]
     */
    public function teamspeakRegistrations()
    {
        return $this->hasMany(\App\Models\TeamSpeak\Registration::class, 'account_id');
    }

    /**
     * Get the first unused registration an account has.
     *
     * @return [type] [description]
     */
    public function getNewTeamspeakRegistrationAttribute()
    {
        return $this->teamspeakRegistrations
            ->filter(
                function ($reg) {
                    return is_null($reg->dbid);
                }
            )
            ->first();
    }
}
