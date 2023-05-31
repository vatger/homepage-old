<?php

namespace App\Models\Network;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PilotClient extends Model
{
    
	protected $table = "networkdata_pilots";

	protected $fillable = [
		'account_id',
		'connected_at',
		'callsign',
		'departure_airport',
        'arrival_airport',
        'alternative_airport',
        'aircraft',
        'cruise_altitude',
        'cruise_tas',
	];

	public $dates = [
		'connected_at',
		'disconnected_at',
		'departed_at',
		'arrived_at',
	];

	protected $appends = [
		'ExpectedArrivalTime'
	];

	/**
	 * Flight completed we assume
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function scopeOffline($query)
	{
		return $query->whereNotNull('disconnected_at');
	}

	/**
	 * Still connected to the network.
	 * Flight is therefore not completed
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function scopeOnline($query)
	{
		return $query->whereNull('disconnected_at');
	}

	/**
	 * Only flights from or to an aerodrome with the given icao.
	 * 
	 * @param  [type] $query [description]
	 * @param  [type] $icao  [description]
	 * @return [type]        [description]
	 */
	public static function scopeWithinAirport($query, $icao)
    {
        return $query->where(function ($subQuery) use ($icao) {
            return $subQuery->where('departure_airport', $icao)
                ->orWhere('arrival_airport', $icao);
        });
    }

    /**
     * Get the expected time remaining.
     *
     * @return float -1 if plane at origin, -2 if plane at destination, -3 if unknown
     *               Else a positive value representing the remaining time in hours
     */
    public function getExpectedArrivalTimeAttribute()
    {
        $destination = Cache::remember(
            'navigation.airport.flight.arrival.'.$this->id, 24 * 60 * 60,
            function () {
                return $this->_loadAirport($this->arrival_airport);
            }
        );
        $origin = Cache::remember(
            'navigation.airport.flight.origin.'.$this->id, 24 * 60 * 60,
            function () {
                return $this->_loadAirport($this->departure_airport);
            }
        );
        // Determine if the flight is at it's origin or destination
        $atOrigin = false;
        $atDestination = false;
        try {
            if ($origin) {
                $atOrigin = $origin->containsCoordinates($this->current_latitude, $this->current_longitude);
            }
            if ($destination) {
                $atDestination = $destination->containsCoordinates($this->current_latitude, $this->current_longitude);
            }
        } catch (Exception $e) {
        }
        if ($atOrigin) {
            return -1;
        }
        if ($atDestination) {
            return -2;
        }
        if ($destination == null) {
            return -3;
        }
        $dlong = ($destination->longitude - $this->current_longitude) * (M_PI / 180);
        $dlat = ($destination->latitude - $this->current_latitude) * (M_PI / 180);
        $a = pow(sin($dlat / 2.0), 2) + cos($this->current_latitude * (M_PI / 180)) * cos($destination->latitude * (M_PI / 180)) * pow(sin($dlong / 2.0), 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = 3956 * $c;

        // return the remaining flight time to destination in hours
        // Avoid division by Zero for planes at parking position
        if ($this->current_groundspeed <= 1) {
            $this->current_groundspeed = 1;
        }

        return $d / ($this->current_groundspeed * 1.15077945);
    }

    /**
     * Get an aerodrome from the database if needed for calculations
     * @param  [type] $icao [description]
     * @return [type]       [description]
     */
    protected function _loadAirport($icao)
    {
        return \App\Models\Navigation\Aerodrome::icao($icao)->first();
    }

}
