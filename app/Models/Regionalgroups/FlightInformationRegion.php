<?php

namespace App\Models\Regionalgroups;

use Illuminate\Database\Eloquent\Model;

class FlightInformationRegion extends Model
{
    
	protected $table = 'regionalgroups_firs';

	/**
	 * All regionalgroups assigned to this fir
	 * 
	 * @return [type] [description]
	 */
	public function regionalgroups()
	{
		return $this->hasMany(\App\Models\Regionalgroups\Regionalgroup::class, 'fir_id', 'id');
	}

}
