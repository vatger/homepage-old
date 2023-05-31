<?php

namespace App\Models\Navigation;

use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Navigation\Aerodrome;

class Station extends Model
{

	// use LogsActivity;

	protected $table = "navigation_stations";
    
	protected static $logAttributes = ['*'];

	protected $appends = [
		'fixedFrequency'
	];

	public function aerodromes()
	{
		return $this->belongsToMany(Aerodrome::class, 'navigation_aerodrome_station', 'station_id', 'aerodrome_id')
			->withPivot('order');
	}

	public function getFixedFrequencyAttribute()
	{
		return number_format($this->frequency, 3);
	}

	public function scopeBookable($query)
	{
		return $query->where('bookable', true);
	}

	public function scopeAtis($query)
	{
		return $query->where('atis', true);
	}

}
