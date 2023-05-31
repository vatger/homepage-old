<?php

namespace App\Models\Statistic;

use Illuminate\Database\Eloquent\Model;

class FlightData extends Model
{

	protected $table = 'statistics_pilots';

	protected $primaryKey = 'id';

	protected $fillable = [
		'account_id',
		'callsign',
        'flight_type',
		'departure_airport',
        'arrival_airport',
        'alternative_airport',
        'aircraft',
        'cruise_altitude',
        'cruise_tas',
        'route',
        'remarks',
        'connected_at',
        'disconnected_at',
        'minutes_online',
	];

	public $dates = [
		'connected_at',
		'disconnected_at',
		'departed_at',
		'arrived_at',
	];

	public function scopeCallsign($query, $callsign)
	{
		return $query->where('callsign', 'LIKE', $callsign.'%');
	}

	public function scopeCid($query, $cid)
	{
		return $query->where('account_id', $cid);
	}

	public function scopeIcao($query, $icao)
	{
		return $query->where('arrival_airport', $icao)->orWhere('departure_airport', $icao);
	}

	public function scopeCompleted($query)
	{
		return $query->whereNotNull('departed_at')->whereNotNull('arrived_at');
	}

}
