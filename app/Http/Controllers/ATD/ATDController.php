<?php

namespace App\Http\Controllers\ATD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ATDController extends Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function solos(Request $request)
	{
        if(!$request->ajax()) abort(403);

        if(Auth::check() && $this->account->setup_completed) {
            $solos = \App\Models\ATD\SoloEndorsement::orderBy('ends_at', 'ASC')
            ->with('station', 'candidate:id,firstname,lastname')
            ->get();
        } else {
            $solos = \App\Models\ATD\SoloEndorsement::orderBy('ends_at', 'ASC')
            ->with('station')
            ->get();
        }
        return $solos;
    }


}
