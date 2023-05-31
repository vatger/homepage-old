<?php

namespace App\Models\Regionalgroups;

use Illuminate\Database\Eloquent\Model;
use App\Models\Membership\Account;
use App\Models\Navigation\Aerodrome;
use App\Models\Regionalgroups\RegionalgroupRequest;
use App\Models\Regionalgroups\FlightInformationRegion;
use App\Models\Regionalgroups\RegionalgroupTemplate;
// use Spatie\Activitylog\Traits\LogsActivity;

class Regionalgroup extends Model
{
	// use LogsActivity;

	protected $table = 'regionalgroups_regionalgroups';

	protected $appends = [
		//'members',
        'membersCount',
		//'guests',
        'guestsCount',
		//'controllers',
		//'pilots',
	];

    /**
	 * The FIR this regionalgroup belongs to
	 * @return [type] [description]
	 */
	public function fir()
	{
		return $this->belongsTo(FlightInformationRegion::class, 'fir_id', 'id');
	}

	/**
	 * All associated accounts
	 * Regardless of guest status
	 *
	 * @return [type] [description]
	 */
	public function accounts()
	{
		return $this->belongsToMany(Account::class, 'regionalgroups_account_regionalgroup', 'regionalgroup_id', 'account_id')
			->withPivot('pilot', 'controller', 'guest')
			->withTimestamps();
	}

	/**
	 * Only full regionalgroup members
	 *
	 * @return [type] [description]
	 */
	public function getMembersAttribute()
	{
		return $this->accounts->reject(function ($acc) {
			return $acc->pivot->guest;
		});
	}
	public function getMembersCountAttribute()
	{
		return count($this->getMembersAttribute());
	}

	/**
	 * Only guest members of the regionalgroup
	 *
	 * @return [type] [description]
	 */
	public function getGuestsAttribute()
	{
		return $this->accounts->filter(function ($acc) {
			return $acc->pivot->guest;
		});
	}
	public function getGuestsCountAttribute()
	{
		return count($this->getGuestsAttribute());
	}

	/**
	 * Only members (guest or full) that are assigned as controllers
	 *
	 * @return [type] [description]
	 */
	public function getControllersAttribute()
	{
		return $this->accounts->filter(function ($acc) {
			return $acc->pivot->controller;
		});
	}

	/**
	 * Members or guests that are assigned as pilots
	 *
	 * @return [type] [description]
	 */
	public function getPilotsAttribute()
	{
		return $this->accounts->filter(function ($acc) {
			return $acc->pivot->pilot;
		});
	}

	/**
	 * The current regionalgroup chief
	 *
	 * @return [type] [description]
	 */
	public function chief()
	{
		return $this->belongsTo(Account::class, 'chief_id', 'id');
	}

	/**
	 * The deputy of the regionalgroup
	 *
	 * @return [type] [description]
	 */
	public function deputy()
	{
		return $this->belongsTo(Account::class, 'deputy_id', 'id');
	}

	/**
	 * All atc/atd mentors of the regionalgroup
	 *
	 * @return [type] [description]
	 */
	public function mentors()
	{
		return $this->belongsToMany(Account::class, 'regionalgroups_mentors', 'regionalgroup_id', 'account_id')
			->withPivot('chief', 'senior');
	}

	/**
	 * All members that are participating in the navigation team of the regionalgroup
	 *
	 * @return [type] [description]
	 */
	public function navigators()
	{
		return $this->belongsToMany(Account::class, 'regionalgroups_navigators', 'regionalgroup_id', 'account_id')
			->withPivot('chief', 'deputy');
	}

	/**
	 * The event team of the regionalgroup
	 *
	 * @return [type] [description]
	 */
	public function eventler()
	{
		return $this->belongsToMany(Account::class, 'regionalgroups_eventler', 'regionalgroup_id', 'account_id')
			->withPivot('chief', 'deputy');
	}

	/**
	 * All requests that have been stated to the regionalgroup
	 *
	 * @return [type] [description]
	 */
	public function requests()
	{
		return $this->hasMany(RegionalgroupRequest::class, 'regionalgroup_id', 'id');
	}

	/**
	 * All aerodromes that are assigned to this regionalgroup
	 *
	 * @return [type] [description]
	 */
	public function aerodromes()
	{
		return $this->belongsToMany(Aerodrome::class, 'navigation_aerodrome_regionalgroup', 'aerodrome_id', 'regionalgroup_id');
	}

    /**
	 * All templates that are assigned to this regionalgroup
	 *
	 * @return [type] [description]
	 */
	public function templates()
	{
		return $this->hasMany(RegionalgroupTemplate::class, 'regionalgroup_id', 'id');
	}

}
