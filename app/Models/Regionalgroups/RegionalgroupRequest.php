<?php

namespace App\Models\Regionalgroups;

use Illuminate\Database\Eloquent\Model;
use App\Models\Membership\Account;
use App\Models\Regionalgroups\Regionalgroup;

class RegionalgroupRequest extends Model
{
    
	protected $table = 'regionalgroups_requests';

	/**
	 * The regionalgroup this request is for
	 * 
	 * @return [type] [description]
	 */
	public function regionalgroup()
	{
		return $this->belongsTo(Regionalgroup::class, 'regionalgroup_id', 'id')->select(['id', 'name']);
	}

	/**
	 * The account that made the request
	 * 
	 * @return [type] [description]
	 */
	public function account()
	{
		return $this->belongsTo(Account::class, 'account_id', 'id');
	}

	/**
	 * The targeting regionalgroup in case of change requests
	 * @return [type] [description]
	 */
	public function destination()
	{
		return $this->belongsTo(Regionalgroup::class, 'destination_id', 'id');
	}

}
