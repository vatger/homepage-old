<?php

namespace App\Models\Membership\Concerns;

use App\Models\Membership\Account\Data;

trait HasDataConcern
{
    /**
     * Does data exists for an account.
     *
     * @return bool [True if data is present]
     */
    public function hasData()
    {
        return Data::where("account_id", $this->id)->exists();
    }

    /**
     * The data object that belongs to the account.
     *
     * @return Data | null
     */
    public function data()
    {
        return $this->hasOne(Data::class, "account_id", "id");
    }

    /**
     * Is the account currently inactive in the global VATSIM scope?
     * @return bool [True if account is inactive]
     */
    public function getIsInactiveAttribute()
    {
        return $this->data->rating_atc == -1;
    }
}
