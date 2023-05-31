<?php

namespace App\Http\Controllers\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Navigation\Aerodrome;
use App\Models\Navigation\StandStatus;
use App\Models\Network\PilotClient;

class AerodromeController extends Controller
{
    
    /**
     * Get a list of all aerodromes
     * 
     * @param  Request $request [description]
     * @param  boolean $local   [description]
     * @return [type]           [description]
     */
	public function getAerodromes(Request $request, $local = true)
	{
		if($request->ajax())
		{
			if($local)
				return Aerodrome::isDe()->with('stations')->get();
			else
				return Aerodrome::with('stations')->get();
		}
	}

	/**
	 * Get a detailed view of a aerodrome
	 * 
	 * @param  Request $request [description]
	 * @param  [type]  $icao    [description]
	 * @return [type]           [description]
	 */
	public function getAerodrome(Request $request, $icao)
	{
		if($request->ajax())
		{
			return Aerodrome::icao($icao)->with('stations', 'charts', 'countryDetail', 'runways', 'navaids')->first();
		}
	}

	/**
	 * Get the current standstatus of an aerodrome
	 * 
	 * @param  Request $request [description]
	 * @param  [type]  $icao    [description]
	 * @return [type]           [description]
	 */
	public function getStandStatus(Request $request, $icao)
	{
		if($request->ajax()) {

			$aerodrome = Aerodrome::icao($icao)->first();

			if($aerodrome === null) return [];

			$standFilePath = storage_path('app') . '/navigation/stands/' .strtolower($aerodrome->icao) . '.csv';

			if(File::exists($standFilePath)) {
				$stands = (new StandStatus($aerodrome->icao, $standFilePath, $aerodrome->latitude, $aerodrome->longitude, false, null))
					->setMaxAircraftAltitude($aerodrome->elevation + 300)
					->setMaxStandDistance(0.02)
					->setMaxDistanceFromAirport(5)
					->parseData();
				if($stands) {
					$standsArray = [];
					foreach ($stands->allStands() as $s) {
						$standsArray[] = $s;
					}
					return $standsArray;
				}
			} else {
				return [];
			}
		}
		abort(403);
	}

	public function getControllerActivity(Request $request, $icao)
	{
		if($request->ajax()) {

			$aerodrome = Aerodrome::icao($icao)->first();

			return $aerodrome->controllerActivity;
		}
		abort(403);
	}

	public function getPilotActivity(Request $request, $icao)
	{
		if($request->ajax()) {

			return PilotClient::online()->withinAirport($icao)->get();;
		}
		abort(403);
	}

	public function getAirports(Request $request, $nonjson = false)
	{
		if($nonjson) {
			return \Illuminate\Support\Facades\Cache::remember(
				'navigation.airport.livemap.nonjson',
				7 * 24 * 60 * 60,
				function () {
					$airportsNonJson = [];
					$airports = \App\Models\Navigation\Aerodrome::where('civilian', true)->get();
					foreach ($airports as $airport) {
						$airportsNonJson[] = [
							'icao' => $airport->icao,
							'name' => $airport->icao . ' (' . $airport->name . ')',
							'lat'	=> floatval($airport->latitude),
							'lng'	=> floatval($airport->longitude),
						];
					}
					return json_encode($airportsNonJson);
				}
			);
		}

		return \Illuminate\Support\Facades\Cache::remember(
			'navigation.airports.livemap',
			7 * 24 * 60 * 60,
			function () {
				$airportsJson = [
					'type' => 'FeatureCollection',
					'features' => [],
				];
				// $airports = \App\Models\Navigation\Aerodrome::all();
				// $airports = \App\Models\Navigation\Aerodrome::isDe()->get();
				// $airports = \App\Models\Navigation\Aerodrome::where('major', true)->get();
				$airports = \App\Models\Navigation\Aerodrome::where('civilian', true)->get();
				foreach ($airports as $airport) {
					$airportsJson['features'][] = [
						'type' => 'Feature',
						'geometry' => [
							'type' => 'Point',
							'coordinates' => [floatval($airport->longitude), floatval($airport->latitude)],
						],
						'properties' => [
							'name' => $airport->icao . ' (' . $airport->name . ')',
						],
					];
				}
				return $airportsJson;
			}
		);
	}

}
