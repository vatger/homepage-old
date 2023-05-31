<?php

namespace App\Models\Navigation;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    
	protected $table = "countries";

	protected $primaryKey = 'id';

	public $incrementing = false;

	public $timestamps = false;

}
