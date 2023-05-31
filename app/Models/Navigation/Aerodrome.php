<?php

namespace App\Models\Navigation;

use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Navigation\Country;
use App\Models\Navigation\Station;
use App\Models\Navigation\Runway;
use App\Models\Navigation\Navaid;
use App\Models\Regionalgroups\Regionalgroup;

class Aerodrome extends Model
{

	// use LogsActivity;

	protected $table = "navigation_aerodromes";

	// protected $appends = ['countryDetail'];

	protected static $logAttributes = ['*'];

	/**
	 * All regionalgroups this aerodrome is assigned to
	 *
	 * @return [type] [description]
	 */
	public function regionalgroups()
	{
		return $this->belongsToMany(Regionalgroup::class, 'navigation_aerodrome_regionalgroup', 'regionalgroup_id', 'aerodrome_id');
	}

	/**
	 * Get all assigned stations
	 * @return [type] [description]
	 */
	public function stations()
	{
		return $this->belongsToMany(Station::class, 'navigation_aerodrome_station', 'aerodrome_id', 'station_id')
			->withPivot('order')
			->orderBy('navigation_aerodrome_station.order', 'ASC');
	}

	/**
	 * All runways this aerodrome has
	 * @return [type] [description]
	 */
	public function runways()
	{
		return $this->hasMany(Runway::class);
	}

	/**
	 * All associated navaids
	 * @return [type] [description]
	 */
	public function navaids()
	{
		return $this->belongsToMany(Navaid::class, 'navigation_aerodrome_navaid', 'navaid_id', 'aerodrome_id');
	}

	/**
	 * The charts associated with this aerodrome
	 *
	 * @return [type] [description]
	 */
	public function charts()
	{
		return $this->belongsToMany(Chart::class, 'navigation_aerodrome_chart', 'aerodrome_id', 'chart_id');
	}

	/**
	 * Get an aerodrome by it's icao
	 * @param  [type] $query [description]
	 * @param  [type] $icao  [description]
	 * @return [type]        [description]
	 */
	public function scopeIcao($query, $icao)
	{
		return $query->where('icao', $icao);
	}

	/**
	 * Get only aerodromes that are assigned to Germany
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function scopeIsDe($query)
	{
		return $query->where('country', 'DE');
	}

	/**
	 * The country the aerodrome is situated at
	 * @return [type] [description]
	 */
	public function countryDetail()
	{
		return $this->belongsTo(Country::class, 'country', 'alpha_2');
	}

	/**
	 * Load the current atc activity at the aerodrome
	 *
	 * @return [type] [description]
	 */
	public function getControllerActivityAttribute()
	{
		if($this->stations->count() > 0) {
			return \App\Models\Network\AtcClient::withCallsignIn(
				$this->stations->pluck('ident')->push('%'.$this->icao.'%')->all()
			)->online()->get();
		}
		return \App\Models\Network\AtcClient::icao('%'.$this->icao.'%')->online()->get();
	}

	/**
	 * Is something in the vicinity of the airport?
	 * @param  [type] $latitude  [description]
	 * @param  [type] $longitude [description]
	 * @return [type]            [description]
	 */
	public function containsCoordinates($latitude, $longitude)
    {
        return $latitude < $this->latitude + 0.06 && $latitude > $this->latitude - 0.06
            && $longitude < $this->longitude + 0.06 && $longitude > $this->longitude - 0.06;
    }

}
