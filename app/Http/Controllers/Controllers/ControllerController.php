<?php

namespace App\Http\Controllers\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ControllerController extends Controller
{
    
	function __construct()
	{
		parent::__construct();
	}

	public function index(Request $request)
	{
		return $this->viewMake('controller.index');
	}

}
