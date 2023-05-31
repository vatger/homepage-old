<?php

namespace App\Models\UTS\ATD;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    
	protected $table = 'uts_atd_trainings';

	public function trainee()
	{
		return $this->belongsTo(\App\Models\Membership\Account::class, 'trainee_id', 'id');
	}

	public function regionalgroup()
	{
		return $this->belongsTo(\App\Models\Regionalgroups\Regionalgroup::class, 'regionalgroup_id', 'id');
	}

	public function sessions()
	{
		return $this->hasMany(\App\Models\UTS\ATD\TrainingSession::class, 'training_id', 'id');
	}

	public function scopeForTrainee($query, $cid)
	{
		return $query->where('trainee_id', $cid);
	}

}
