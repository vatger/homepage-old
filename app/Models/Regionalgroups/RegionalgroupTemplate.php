<?php

namespace App\Models\Regionalgroups;

use Illuminate\Database\Eloquent\Model;
use App\Models\Regionalgroups\Regionalgroup;

class RegionalgroupTemplate extends Model
{

	protected $table = 'regionalgroups_templates';

	/**
	 * The regionalgroup this template is for
	 *
	 * @return [type] [description]
	 */
	public function regionalgroup()
	{
		return $this->belongsTo(Regionalgroup::class, 'regionalgroup_id', 'id')->select(['id', 'name']);
	}

}
