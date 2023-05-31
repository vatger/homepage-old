<?php

namespace App\Http\Controllers\Administration\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
	function __construct()
	{
		parent::__construct();

		$this->middleware(function ($request, $next) {

			if(!$this->account->hasPermission('administration'))
				abort(403);

			return $next($request);
		});
	}

	public function index()
	{
		return $this->viewMake('administration.dashboard.index');
	}

	public function getStatistics(Request $request)
	{
		if(!$request->ajax()) abort(403);

		$accounts = \App\Models\Membership\Account::with('data')->get();
        $memberCount = $accounts->count();
        $inactive = $accounts->where('data.active', false)->count();
        $nonDivision = $accounts->whereNotIn('data.subdivision_code', ['GER'])->count();
        $division = $accounts->where('data.subdivision_code', 'GER')->count();

        $now = \Carbon\CarbonImmutable::now();
        $yesterday = $now->subDay();

        $flights = \App\Models\Statistic\FlightData::whereBetween('disconnected_at', [$yesterday, $now])->count();
        $atcs = \App\Models\Statistic\AtcData::whereBetween('disconnected_at', [$yesterday, $now])->count();

        return response()->json(
            [
                'members' => $memberCount,
                'inactive' => $inactive,
                'nonDivision' => $nonDivision,
                'division' => $division,
                'flights' => $flights,
                'atcs' => $atcs,
            ]
        );

	}

}
