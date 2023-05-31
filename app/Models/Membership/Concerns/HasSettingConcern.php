<?php

namespace App\Models\Membership\Concerns;

use App\Models\Membership\Account\Setting;

trait HasSettingConcern
{
    /**
     * Does data exists for an account.
     *
     * @return bool [True if data is present]
     */
    public function hasSetting()
    {
        return Setting::where('account_id', $this->id)->exists();
    }

    /**
     * The data object that belongs to the account.
     *
     * @return \App\Models\Membership\Account\Setting | null
     */
    public function setting()
    {
        return $this->hasOne(Setting::class, 'account_id', 'id');
    }
}
