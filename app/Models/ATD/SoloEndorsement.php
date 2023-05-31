<?php

namespace App\Models\ATD;

use Illuminate\Database\Eloquent\Model;

class SoloEndorsement extends Model
{
    protected $table = 'uts_atd_solo_clearances';

    protected $dates = [
        'created_at',
        'updated_at',
        'begins_at',
        'ends_at',
    ];

    /**
     * The candidate this solo endorsement is assigned to.
     *
     * @return [type] [description]
     */
    public function candidate()
    {
        return $this->belongsTo(\App\Models\Membership\Account::class, 'candidate_id', 'id');
    }

    /**
     * The solo phase this endorsement is bound to.
     *
     * @return [type] [description]
     */
    public function phase()
    {
        return $this->belongsTo(\App\Models\ATD\SoloPhase::class, 'solophase_id', 'id');
    }

    /**
     * The atc station this endorsement is limited to.
     *
     * @return [type] [description]
     */
    public function station()
    {
        return $this->belongsTo(\App\Models\Navigation\Station::class, 'station_id', 'id');
    }

    /**
     * Calculates the date this endorsement ends.
     *
     * @param \Carbon\Carbon|null $startDate   [description]
     * @param [type]              $isExtension [description]
     *
     * @return [type] [description]
     */
    public function calculateEndDate(\Carbon\Carbon $startDate = null, $isExtension = false)
    {
        if (null == $startDate) {
            $startDate = \Carbon\Carbon::now()->utc();
        }

        if ($isExtension) {
            // Extensions are always 30 days
            return $startDate->addDays(30);
        }

        if (null != $this->phase) {
            $endDate = $startDate->addDays($this->phase->days);

            return $endDate;
        } else {
            return false;
        }
    }

}
