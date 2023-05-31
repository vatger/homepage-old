<?php

namespace App\Http\Controllers\Pilots;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PilotController extends Controller
{
    
	function __construct()
	{
		parent::__construct();
	}

	public function index(Request $request)
	{
		return $this->viewMake('pilots.index');
	}

}
