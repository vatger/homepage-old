<?php

namespace App\Models\UTS\ATD;

use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    
	protected $table = 'uts_atd_sessions';

	public $dates = [
		'started_at',
		'ended_at',
	];

	public $appends = [
		'typeString',
	];

	const TYPE_THEORY = 1;
	const TYPE_SIM = 2;
	const TYPE_ONLINE = 3;

	public function training()
	{
		return $this->belongsTo(\App\Models\UTS\ATD\Training::class, 'training_id', 'id');
	}

	public function mentor()
	{
		return $this->belongsTo(\App\Models\Membership\Account::class, 'mentor_id', 'id');
	}

	public function secondMentor()
	{
		return $this->belongsTo(\App\Models\Membership\Account::class, 'second_mentor_id', 'id');
	}

	public function station()
	{
		return $this->belongsTo(\App\Models\Navigation\Station::class, 'station_id', 'id');
	}

	public function getTypeStringAttribute()
	{
		$tts = '';
		switch ($this->type) {
			case self::TYPE_THEORY:
				$tts = 'Theory Session';
				break;
			case self::TYPE_SIM:
				$tts = 'Sim Session';
				break;
			case self::TYPE_ONLINE:
				$tts = 'Online Session';
				break;
			default:
				break;
		}
		return $tts;
	}

}
