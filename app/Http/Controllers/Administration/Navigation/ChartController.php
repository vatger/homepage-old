<?php

namespace App\Http\Controllers\Administration\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Navigation\Chart;
use App\Notifications\Administration\Navigation\ChartCreatedNotification;
use App\Notifications\Administration\Navigation\ChartUpdatedNotification;
use App\Notifications\Administration\Navigation\ChartRemovedNotification;

class ChartController extends Controller
{

	function __construct()
	{
		parent::__construct();

		$this->middleware(function($request, $next) {

			if(!$this->account->hasAnyPermission('administration.navigation', 'administration.navigation.rg'))
				abort(403);

			return $next($request);
		});
	}

	public function index(Request $request)
	{
		if($request->ajax()) {
			return Chart::orderBy('name', 'ASC')->get();
		}
		abort(403);
	}

	public function create(Request $request)
	{
		if($request->ajax()) {

			$validated = $request->validate([
				'chart.name' => 'required|string',
				'chart.href' => 'required|string',
                'chart.published' => 'nullable|boolean',
                'chart.public_available' => 'nullable|boolean',
				'chart.airac' => 'nullable|numeric',
				'chart.type' => 'required|in:aoi,afc,agc,apc,sid,star,iac',
				'chart.fri' => 'required|in:ifr,vfr'
			]);

			$chart = new Chart();
			$chart->name = $validated['chart']['name'];
			$chart->href = $validated['chart']['href'];
            $chart->published = array_key_exists('published', $validated['chart']) ? boolval($validated['chart']['published']) : false;
            $chart->public_available = array_key_exists('public_available', $validated['chart']) ? boolval($validated['chart']['public_available']) : false;
			$chart->airac = array_key_exists('airac', $validated['chart']) ?intval($validated['chart']['airac']) : 0; 
			$chart->type = $validated['chart']['type'];
			$chart->fri = $validated['chart']['fri'];

			$chart->save();

			$this->account->notify(new ChartCreatedNotification($chart));

			return $chart;

		}
		abort(403);
	}

	public function update(Request $request, Chart $chart)
	{
		if($request->ajax()) {

			$validated = $request->validate([
				'chart.id' => 'required|exists:navigation_charts,id',
				'chart.name' => 'required|string',
				'chart.href' => 'required|string',
                'chart.published' => 'nullable|boolean',
                'chart.public_available' => 'nullable|boolean',
				'chart.airac' => 'required|numeric',
				'chart.type' => 'required|in:aoi,afc,agc,apc,sid,star,iac',
				'chart.fri' => 'required|in:ifr,vfr'
			]);

			$chart->name = $validated['chart']['name'];
			$chart->href = $validated['chart']['href'];
            $chart->published = boolval($validated['chart']['published']);
            $chart->public_available = boolval($validated['chart']['public_available']);
			$chart->airac = intval($validated['chart']['airac']);
			$chart->type = $validated['chart']['type'];
			$chart->fri = $validated['chart']['fri'];

			$chart->save();

			$this->account->notify(new ChartUpdatedNotification($chart));

			return $chart;

		}
		abort(403);
	}

	public function remove(Request $request, Chart $chart)
	{
		if($request->ajax()) {

			$chartName = $chart->name;

			$chart->aerodromes()->detach();

			$chart->delete();

			$this->account->notify(new ChartRemovedNotification($chartName));

			return $chartName;
		}
		abort(403);
	}

}
