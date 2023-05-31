<?php

namespace App\Models\ATD;

use Illuminate\Database\Eloquent\Model;

class SoloPhase extends Model
{
    protected $table = 'uts_atd_solophases';

    /**
     * Get all solo endorsements that are bound to this phase.
     *
     * @return [type] [description]
     */
    public function endorsements()
    {
        return $this->hasMany(\App\Models\ATD\SoloEndorsements::class, 'solophase_id', 'id');
    }
}
