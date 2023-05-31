<?php

namespace App\Models\Statistic;

use Illuminate\Database\Eloquent\Model;

class AtcData extends Model
{
    
	protected $table = 'statistics_atc';

	protected $primaryKey = 'id';

	protected $fillable = [
		'account_id',
        'callsign',
        'frequency',
        'qualification_id',
        'facility_type',
        'connected_at',
        'disconnected_at',
        'minutes_online',
	];

	public $dates = [
		'connected_at',
		'disconnected_at',
	];

	public function scopeCallsign($query, $callsign)
	{
		return $query->where('callsign', 'LIKE', '%'.$callsign.'%');
	}

	public function scopeCid($query, $cid)
	{
		return $query->where('account_id', $cid);
	}

	public function scopeFrequency($query, $fq)
	{
		return $query->where('frequency', $fq);
	}
	
}
