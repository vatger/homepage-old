<?php

namespace App\Http\Controllers\Regionalgroup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Regionalgroups\Regionalgroup;
use App\Models\Regionalgroups\RegionalgroupRequest;

class RegionalgroupController extends Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function index(Request $request)
	{
		if($request->ajax()){

			$regionalgroups = $this->account->regionalgroups;
            foreach ($regionalgroups as $rg ) {
                $rg->setAppends([]);
            }
			return $regionalgroups;

		}
		abort(403);
	}

	public function news(Request $request, Regionalgroup $regionalgroup)
	{
		if($request->ajax()) {
			if($this->account->isMemberOfRegionalgroup($regionalgroup) || $this->account->isGuestOfRegionalgroup($regionalgroup)) {
				// Grab the news
			}
		}
		abort(403);
	}

}
