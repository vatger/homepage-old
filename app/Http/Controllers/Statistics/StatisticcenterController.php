<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatisticcenterController extends Controller
{

	function __construct()
	{
		parent::__construct();
	}
    
	public function index()
	{
		return $this->viewMake('frontend.statistics.index');
	}

}
