<?php

namespace App\Models\Filebase;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{

	protected $table = "imagestore";

	protected $appends = [
		'href',
	];

	public function scopeAccountId($query, $id)
	{
		return $query->where('account_id', $id);
	}

	public function getHrefAttribute()
	{
		return config('app.url').'/resources/image/'.$this->id;
	}

	public function account() {
		return $this->belongsTo(\App\Models\Membership\Account::class, 'account_id', 'id');
	}

}
