<?php

namespace App\Models\Membership\Concerns;

use App\Models\Regionalgroups\Regionalgroup;
use App\Models\Regionalgroups\RegionalgroupRequest;

trait HasRegionalgroupConcern {

	/**
	 * All regionalgroups an account is assigned to in any way
	 * 
	 * @return [type] [description]
	 */
	public function regionalgroups()
	{
		return $this->belongsToMany(Regionalgroup::class, 'regionalgroups_account_regionalgroup', 'account_id', 'regionalgroup_id')
			->withPivot('pilot', 'controller', 'guest')
			->with('fir')
            ->withTimestamps();
	}

	/**
	 * All requests to regionalgroups from this account
	 * 
	 * @return [type] [description]
	 */
	public function regionalgroupRequests()
	{
		return $this->hasMany(RegionalgroupRequest::class, 'account_id', 'id');
	}

	/**
	 * Fullmember of regionalgroup?
	 * 
	 * @param  Regionalgroup $regionalgroup [description]
	 * @return boolean                      [description]
	 */
	public function isMemberOfRegionalgroup(Regionalgroup $regionalgroup)
	{
		return $regionalgroup->members->contains($this);
	}

	/**
	 * Assigned as guest?
	 * 
	 * @param  Regionalgroup $regionalgroup [description]
	 * @return boolean                      [description]
	 */
	public function isGuestOfRegionalgroup(Regionalgroup $regionalgroup)
	{
		return $regionalgroup->guests->contains($this);
	}

	/**
	 * Is the account a controller at the regionalgroup
	 * 
	 * @param  Regionalgroup $regionalgroup [description]
	 * @return boolean                      [description]
	 */
	public function isControllerOfRegionalgroup(Regionalgroup $regionalgroup)
	{
		return $regionalgroup->controllers->contains($this);
	}

	/**
	 * Is the account assigned as a pilot
	 * 
	 * @param  Regionalgroup $regionalgroup [description]
	 * @return boolean                      [description]
	 */
	public function isPilotOfRegionalgroup(Regionalgroup $regionalgroup)
	{
		return $regionalgroup->pilots->contains($this);
	}

	/**
	 * Is the account assigend as a mentor to the given regionalgroup
	 * 
	 * @param  Regionalgroup $regionalgroup [description]
	 * @return boolean                      [description]
	 */
	public function isMentorOfRegionalgroup(Regionalgroup $regionalgroup)
	{
		return $regionalgroup->mentors->contains($this);
	}

	/**
	 * Is the account assigned as a navigator to the regionalgroup
	 * @param  Regionalgroup $regionalgroup [description]
	 * @return boolean                      [description]
	 */
	public function isNavigatorOfRegionalgroup(Regionalgroup $regionalgroup)
	{
		return $regionalgroup->navigators->contains($this);
	}

	/**
	 * Does the account belong to the event team of a given regionalgroup
	 * 
	 * @param  Regionalgroup $regionalgroup [description]
	 * @return boolean                      [description]
	 */
	public function isEventlerOfRegionalgroup(Regionalgroup $regionalgroup)
	{
		return $regionalgroup->eventler->contains($this);
	}

}