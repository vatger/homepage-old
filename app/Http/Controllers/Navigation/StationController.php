<?php

namespace App\Http\Controllers\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StationController extends Controller
{
    
	public function getBookableStations(Request $request, $bookable = true)
	{
		if($request->ajax())
		{
			if($bookable)
				return \App\Models\Navigation\Station::bookable()->orderBy('ident', 'ASC')->get();
			else
				return \App\Models\Navigation\Station::orderBy('ident', 'ASC')->get();
		}
	}

}
