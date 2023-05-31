<?php

namespace App\Models\Navigation;

use Illuminate\Database\Eloquent\Model;
use App\Models\Navigation\Aerodrome;

class Navaid extends Model
{

	protected $table = 'navigation_navaids';
	
	public const FREQUENCY_BAND_MHZ = 1;
    public const FREQUENCY_BAND_KHZ = 2;

    public const TYPE_NDB = 1;
    public const TYPE_VOR = 2;
    public const TYPE_VORDME = 3;
    public const TYPE_DME = 4;
    public const TYPE_ILS = 5;
    public const TYPE_TACAN = 6;

    public $appends = [
    	'typeString',
    	'frequencyBandString'
    ];

	public function aerodromes() {
		return $this->belongsToMany(Aerodrome::class, 'navigation_aerodrome_navaid', 'aerodrome_id', 'navaid_id');
	}

	public function getTypeStringAttribute()
	{
		$ts = '';
		switch ($this->type) {
			case self::TYPE_NDB:
				$ts = 'NDB';
				break;
			case self::TYPE_VOR:
				$ts = 'VOR';
				break;
			case self::TYPE_VORDME:
				$ts = 'VOR / DME';
				break;
			case self::TYPE_DME:
				$ts = 'DME';
				break;
			case self::TYPE_ILS:
				$ts = 'ILS';
				break;
			case self::TYPE_TACAN:
				$ts = 'TACAN';
				break;
			default:
				$ts = 'Unknown';
				break;
		}

		return $ts;
	}

	public function getFrequencyBandStringAttribute()
	{
		$fbs = '';
		switch ($this->frequency_band) {
            case self::FREQUENCY_BAND_MHZ:
                $fbs = 'MHz';
                break;
            case self::FREQUENCY_BAND_KHZ:
                $fbs = 'KHz';
                break;
            default:
                $fbs = 'Unknown';
                break;
        }

		return $fbs;
	}
	
}