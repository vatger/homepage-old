<?php

namespace App\Http\Controllers\Administration\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Navigation\Aerodrome;
use App\Models\Navigation\Station;
use App\Models\Navigation\Chart;
use App\Models\Navigation\Navaid;
use App\Models\Navigation\Runway;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Notifications\Administration\Navigation\AerodromeUpdatedNotification;

class AerodromeController extends Controller
{

	function __construct()
	{
		parent::__construct();

		$this->middleware(function ($request, $next) {

			if(!$this->account->hasAnyPermission('administration.navigation', 'administration.navigation.rg'))
				abort(403);

			return $next($request);
		});
	}

	/**
	 * Get aerodrome details based upon account permissions
	 *
	 * @param  Request $request [description]
	 * @param  [type]  $icao    [description]
	 * @return [type]           [description]
	 */
	public function show(Request $request, $icao)
	{
		if($request->ajax()) {

			// 1. Grab the aerodrome
			$aerodrome = $this->_getAerodromeByIcao($icao);
			// 2. Get all assigned regionalgroups
			$regionalgroups = $aerodrome->regionalgroups;
			// 3. If no regionalgroup is assigned... return airfield if one of the permissions is set
			if(count($regionalgroups) == 0 && ($this->account->hasPermission('administration.navigation') || $this->account->hasPermission('administration.navigation.rg'))) {
				return $aerodrome;
			}
			// 4. Regardless of regionalgroup assignment.
			// vACC NAV always has the permissions to modify
			if($this->account->hasPermission('administration.navigation'))
			{
				return $aerodrome;
			}

			// Check if aerodrome is allowed to be modified by this account
			// as it is assigned to at least one regionalgroup
			 if ($this->account->hasPermission('administration.navigation.rg')) {
				foreach ($regionalgroups as $rg) {
					if($this->account->isNavigatorOfRegionalgroup($rg)) {
						return $aerodrome;
					}
				}
			}
			abort(403);
		}
	}

	/**
	 * Creates an aerodrome
	 * @param  Request   $request   [description]
	 * @return [type]               [description]
	 */
	public function createAerodrome(Request $request)
	{
		if($request->ajax()) {
			$validated = $request->validate([
				'newAerodrome.name' => 'required|string',
				'newAerodrome.icao' => 'required|string|size:4',
				'newAerodrome.iata' => 'string|size:3',
				'newAerodrome.description' => 'string|nullable',
				'newAerodrome.country' => 'required|string|size:2',
				'newAerodrome.state' => 'required|string',
				'newAerodrome.city' => 'required|string',
			]);

			// Set all general information even regionalgroup navigators can edit
			$aerodrome = new Aerodrome();
			$aerodrome->name = $validated['newAerodrome']['name'];
			$aerodrome->icao = $validated['newAerodrome']['icao'];
			$aerodrome->iata = $validated['newAerodrome']['iata'] != null ? $validated['newAerodrome']['iata'] : '';
			$aerodrome->description = $validated['newAerodrome']['description'] != null ? $validated['newAerodrome']['description'] : '';
			$aerodrome->country = $validated['newAerodrome']['country'];
			$aerodrome->state = $validated['newAerodrome']['state'];
			$aerodrome->city = $validated['newAerodrome']['city'];
			
			return $this->_reloadAerodrome($aerodrome);
		}
	}

	/**
	 * Updates an aerodrome based upon the permissions a user has
	 * @param  Request   $request   [description]
	 * @param  Aerodrome $aerodrome [description]
	 * @return [type]               [description]
	 */
	public function updateGeneral(Request $request, Aerodrome $aerodrome)
	{
		if($request->ajax()) {
			$validated = $request->validate([
				'aerodrome.id' => 'required|exists:navigation_aerodromes,id',
				'aerodrome.name' => 'required|string',
				'aerodrome.icao' => 'required|string|size:4',
				'aerodrome.iata' => 'string|size:3',
				'aerodrome.description' => 'string|nullable',
				'aerodrome.latitude' => 'required',
				'aerodrome.longitude' => 'required',
				'aerodrome.elevation' => 'required',
				'aerodrome.regionalgroups' => 'nullable',
				'aerodrome.major' => 'required|boolean',
				'aerodrome.military' => 'required|boolean',
				'aerodrome.civilian' => 'required|boolean',
				'aerodrome.active' => 'required|boolean',
				'aerodrome.country' => 'required|string|size:2',
				'aerodrome.state' => 'required|string',
				'aerodrome.city' => 'required|string',
				'aerodrome.wiki_link' => 'string|nullable',
			]);

			// Set all general information even regionalgroup navigators can edit

			$aerodrome->name = $validated['aerodrome']['name'];
			$aerodrome->icao = $validated['aerodrome']['icao'];
			$aerodrome->iata = $validated['aerodrome']['iata'] != null ? $validated['aerodrome']['iata'] : '';
			$aerodrome->description = $validated['aerodrome']['description'] != null ? $validated['aerodrome']['description'] : '';
			$aerodrome->latitude = floatval($validated['aerodrome']['latitude']);
			$aerodrome->longitude = floatval($validated['aerodrome']['longitude']);
			$aerodrome->elevation = intval($validated['aerodrome']['elevation']);
			$aerodrome->major = $validated['aerodrome']['major'];
			$aerodrome->military = $validated['aerodrome']['military'];
			$aerodrome->civilian = $validated['aerodrome']['civilian'];
			$aerodrome->active = $validated['aerodrome']['active'];
			$aerodrome->country = $validated['aerodrome']['country'];
			$aerodrome->state = $validated['aerodrome']['state'];
			$aerodrome->city = $validated['aerodrome']['city'];
			$aerodrome->wiki_link = $validated['aerodrome']['wiki_link'] != null ? $validated['aerodrome']['wiki_link'] : '';

			// A normal regionalgroup navigator is not allowed to assign aerodromes to regionalgroups
			if($this->account->hasPermission('administration.navigation'))
			{
				$aerodrome->regionalgroups()->detach();
				// If the account does not belong to the vACC Germany Navigation Team
				// the regionalgroup assignment must not be made.
				if($validated['aerodrome']['regionalgroups'] != null) {
					foreach ($validated['aerodrome']['regionalgroups'] as $rg) {
						$aerodrome->regionalgroups()->attach($rg['id']);
					}
				}
			}

			return $this->_reloadAerodrome($aerodrome);
		}
	}

	/**
	 * Get a list of available regionalgroups
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getAvailableRegionalgroups(Request $request)
	{
		if($request->ajax()) {
			if($this->account->hasPermission('administration.navigation')) {
				return \App\Models\Regionalgroups\Regionalgroup::orderBy('name', 'ASC')->get();
			} else {
				return [];
			}
		}
		abort(403);
	}

	/**
	 * Updates the order of assigned stations
	 *
	 * @param  Request   $request   [description]
	 * @param  Aerodrome $aerodrome [description]
	 * @return [type]               [description]
	 */
	public function updateStationOrder(Request $request, Aerodrome $aerodrome)
	{
		if($request->ajax()) {
			$stations = $request->stations;
			foreach ($stations as $s) {
				$station = Station::find($s['id']);
				$aerodrome->stations()->updateExistingPivot($station, ['order' => $s['pivot']['order']], false);
			}

			return $this->_reloadAerodrome($aerodrome);
		}
		abort(403);
	}

	/**
	 * Adds a station to the aerodrome
	 *
	 * @param Request   $request   [description]
	 * @param Aerodrome $aerodrome [description]
	 */
	public function addStation(Request $request, Aerodrome $aerodrome)
	{
		if($request->ajax()) {
			$validated = $request->validate([
				'station' => 'required|exists:navigation_stations,id'
			]);

			$station = Station::find($validated['station']);
			if($station->id === $validated['station']) {
				// Get the amount of attached stations to set the order
				$stationsCount = $aerodrome->stations->count();
				$aerodrome->stations()->attach($station, ['order' => $stationsCount + 1]);

				return $this->_reloadAerodrome($aerodrome);
			}
		}
		abort(403);
	}

	/**
	 * Removes a station from the aerodrome
	 *
	 * @param  Request   $request   [description]
	 * @param  Aerodrome $aerodrome [description]
	 * @param  Station   $station   [description]
	 * @return [type]               [description]
	 */
	public function removeStation(Request $request, Aerodrome $aerodrome, Station $station)
	{
		if($request->ajax()) {
			$aerodrome->stations()->detach($station);

			return $this->_reloadAerodrome($aerodrome);
		}
		abort(403);
	}

	/**
	 * Adds a chart to the aerodrome
	 *
	 * @param Request   $request   [description]
	 * @param Aerodrome $aerodrome [description]
	 */
	public function addChart(Request $request, Aerodrome $aerodrome)
	{
		if($request->ajax()) {

			$validated = $request->validate([
				'chart' => 'required|exists:navigation_charts,id'
			]);

			$chart = Chart::find($validated['chart']);

			$aerodrome->charts()->attach($chart);

			return $this->_reloadAerodrome($aerodrome);
		}
		abort(403);
	}

	/**
	 * Removes a chart from the aerodrome
	 *
	 * @param  Request   $request   [description]
	 * @param  Aerodrome $aerodrome [description]
	 * @param  Chart     $chart     [description]
	 * @return [type]               [description]
	 */
	public function removeChart(Request $request, Aerodrome $aerodrome, Chart $chart)
	{
		if($request->ajax()) {

			$aerodrome->charts()->detach($chart);

			return $this->_reloadAerodrome($aerodrome);
		}
		abort(403);
	}

	/**
	 * Associate a given navaid with this aerodrome
	 *
	 * @param Request   $request   [description]
	 * @param Aerodrome $aerodrome [description]
	 */
	public function addNavaid(Request $request, Aerodrome $aerodrome)
	{
		if($request->ajax()) {
			$validated = $request->validate([
				'navaid' => 'required|exists:navigation_navaids,id',
			]);

			$navaid = Navaid::find($validated['navaid']);

			$aerodrome->navaids()->attach($navaid);

			return $this->_reloadAerodrome($aerodrome);
		}
		abort(403);
	}

	/**
	 * Detach a given navaid from this aerodrome
	 *
	 * @param  Request   $request   [description]
	 * @param  Aerodrome $aerodrome [description]
	 * @param  Navaid    $navaid    [description]
	 * @return [type]               [description]
	 */
	public function removeNavaid(Request $request, Aerodrome $aerodrome, Navaid $navaid)
	{
		if($request->ajax()) {
			$aerodrome->navaids()->detach($navaid);

			return $this->_reloadAerodrome($aerodrome);
		}
		abort(403);
	}

	public function addRunway(Request $request, Aerodrome $aerodrome)
	{
		if($request->ajax()) {

			$validated = $request->validate([
				'newRunway.ident' => 'required|string|max:3',
				'newRunway.heading' => 'required',
				'newRunway.length' => 'required',
				'newRunway.width' => 'required',
				'newRunway.surface_type' => 'required|in:1,2,3,4,5,6',
				'newRunway.opposite_id' => 'nullable|exists:navigation_runways,id'
			]);

			$newRunway = new Runway;
			$newRunway->aerodrome_id = $aerodrome->id;
			$newRunway->ident = $validated['newRunway']['ident'];
			$newRunway->heading = $validated['newRunway']['heading'];
			$newRunway->length = $validated['newRunway']['length'];
			$newRunway->width = $validated['newRunway']['width'];
			$newRunway->surface_type = $validated['newRunway']['surface_type'];
			$newRunway->opposite_id = $validated['newRunway']['opposite_id'] ?? null;

			$newRunway->save();

			return $this->_reloadAerodrome($aerodrome);
		}
	}

	public function editRunway(Request $request, Aerodrome $aerodrome, Runway $runway)
	{
		if($request->ajax()) {

			$validated = $request->validate([
				'editRunway.ident' => 'required|string|max:3',
				'editRunway.heading' => 'required',
				'editRunway.length' => 'required',
				'editRunway.width' => 'required',
				'editRunway.surface_type' => 'required|in:1,2,3,4,5,6',
				'editRunway.opposite_id' => 'nullable|exists:navigation_runways,id'
			]);

			$runway->ident = $validated['editRunway']['ident'];
			$runway->heading = $validated['editRunway']['heading'];
			$runway->length = $validated['editRunway']['length'];
			$runway->width = $validated['editRunway']['width'];
			$runway->surface_type = $validated['editRunway']['surface_type'];
			$runway->opposite_id = $validated['editRunway']['opposite_id'] ?? null;

			$runway->save();

			return $this->_reloadAerodrome($aerodrome);
		}
	}

	public function removeRunway(Request $request, Aerodrome $aerodrome, Runway $runway)
	{
		if($request->ajax()) {
			$runway->delete();

			return $this->_reloadAerodrome($aerodrome);
		}
	}

	/**
     * Download the current stand definition file of an aerodrome.
     *
     * @param Request                          $request   [description]
     * @param \App\Models\Navigation\Aerodrome $aerodrome [description]
     *
     * @return [type] [description]
     */
    public function getStands(Request $request, Aerodrome $aerodrome)
    {
        $standFile = 'navigation/stands/'.strtolower($aerodrome->icao).'.csv';
        if (File::exists(storage_path().'/app/'.$standFile)) {
            return Storage::download($standFile);
        } else {
            return Storage::download('navigation/stands/new.csv');
        }
    }

    /**
     * Update the stand definition file of an aerodrome.
     *
     * @param Request                          $request   [description]
     * @param \App\Models\Navigation\Aerodrome $aerodrome [description]
     *
     * @return [type] [description]
     */
    public function updateStands(Request $request, Aerodrome $aerodrome)
    {
        $validated = $request->validate(
            [
                'newStandFile' => 'required|file',
            ]
        );

        $newStandFile = $request->file('newStandFile');

        $result = Storage::putFileAs(
            'navigation/stands/',
            $newStandFile,
            strtolower($aerodrome->icao).'.csv'
        );

        return $this->_reloadAerodrome($aerodrome);
    }


	/**
	 * Get an aerodrome by it's identifing icao code
	 * This also loads contry and regionalgroup details
	 *
	 * @param  [type] $icao [description]
	 * @return [type]       [description]
	 */
	private function _getAerodromeByIcao($icao)
	{
		// Load needed stuff here
		$aerodrome = Aerodrome::where('icao', $icao)->firstOrFail();
		$aerodrome->loadMissing('countryDetail', 'regionalgroups', 'stations', 'charts', 'navaids', 'runways.opposite');
		// Return the completely loaded model
		return $aerodrome;

	}

	/**
	 * Save and reload the aerodrome
	 * Then return the refreshed model
	 *
	 * @param  Aerodrome $aerodrome [description]
	 * @return [type]               [description]
	 */
	private function _reloadAerodrome(Aerodrome $aerodrome)
	{
		$aerodrome->save();
		$aerodrome->refresh();

		$aerodrome->loadMissing('countryDetail', 'regionalgroups', 'stations', 'charts', 'navaids', 'runways.opposite');

		$this->account->notify(new AerodromeUpdatedNotification($aerodrome));

		return $aerodrome;
	}

}
