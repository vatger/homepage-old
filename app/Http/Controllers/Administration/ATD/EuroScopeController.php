<?php

namespace App\Http\Controllers\Administration\ATD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * EuroScope Controller Class
 *
 * To calculate the initial heading of a plane use this formula
 * ((int) ( Heading * 2.88 + 0.5 )) << 2 )
 */
class EuroScopeController extends Controller
{

	private $_maximumFlightsPerSession = 1000;

	private $_departureRoutes = [];
	private $_arrivalRoutes = [];

	private $_scenario = '';

	private $_aerodrome = null;
	private $_holdings = null;
	private $_departures = 50;
	private $_arrivals = 50;
	private $_squawkRange = ['min' => 0001, 'max' => 7777];
	private $_initialPseudoPilot = '';
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Parameter validation and initial calculations
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function createScenario(Request $request)
	{
		$validated = $request->validate([
			'icao' => 'required|string|size:4',
			'holdings' => 'nullable',
			'trafficScale' => 'required|numeric|min:1|max:100',
			'depArrScale' => 'required|numeric|min:0|max:100',
			'minSquawk' => 'required|numeric|min:0001|max:7777|lt:maxSquawk',
			'maxSquawk' => 'required|numeric|min:0001|max:7777|gt:minSquawk',
			'initialPseudo' => 'required|string',
		]);

		$this->_aerodrome = \App\Models\Navigation\Aerodrome::icao($validated['icao'])->with('stations', 'runways')->first();
		$this->_holdings = $validated['holdings'] ?? false;
		$this->_squawkRange['min'] = $validated['minSquawk'];
		$this->_squawkRange['max'] = $validated['maxSquawk'];
		$this->_initialPseudoPilot = $validated['initialPseudo'];

		$totalTraffic = $this->_maximumFlightsPerSession * ($validated['trafficScale'] / 100);

		$this->_departures = round($totalTraffic * (($validated['depArrScale'] / 100)));
		$this->_arrivals = round($totalTraffic * (1 - $validated['depArrScale'] / 100));

		$this->_scenario = "BUILDING SCENARIO WITH:\n";
		$this->_scenario.= "Total Traffic Count: ".$totalTraffic."\n";
		$this->_scenario.= "Departure Traffic Count: ".$this->_departures."\n";
		$this->_scenario.= "Arrival Traffic Count: ".$this->_arrivals."\n;=============================================\n";

		return $this->_buildScenario($request);
	}

	/**
	 * Logic to build up a scenario
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	private function _buildScenario(Request $request)
	{
		$this->_scenario.= "AIRPORT_ALT:".$this->_aerodrome->elevation."\n";

		$this->_scenario.= "\n";
		foreach ($this->_aerodrome->runways as $runway) {
			$this->_scenario.= "ILS".$runway->ident.":<Latitude Goes Here>:<Logitude Goes Here>:".$runway->heading."\n";
		}

		if($this->_holdings !== false) {
			$this->_scenario.= "\n";
			$holdings = explode("\n", $this->_holdings);
			foreach ($holdings as $h) {
				$this->_scenario.= 'HOLDING:'.$h."\n";
			}
		}

		$this->_scenario.= "\n";
		foreach ($this->_aerodrome->stations as $station) {
			$this->_scenario.= "CONTROLLER:".$station->ident.":".$station->fixedFrequency."\n";
		}

		$this->_scenario.= "\n";
		$this->_departureRoutes = $this->_findFlightplans($this->_aerodrome->icao, true);
		$this->_arrivalRoutes = $this->_findFlightplans($this->_aerodrome->icao, false);

		foreach ($this->_departureRoutes as $dr) {
			$this->_renderFlight($dr);
		}
		foreach ($this->_arrivalRoutes as $ar) {
			$this->_renderFlight($ar);
		}

		if($request->ajax()) {
			return $this->_scenario;
		}
	}

	/**
	 * Build the flightplan format for the scenario file
	 * 
	 * @param  [type] $flight [description]
	 * @return [type]         [description]
	 */
	protected function _renderFlight($flight)
	{
		// @<transponder flag>:<callsign>:<squawk code>:1:<latitude>:<longitude>:<altitude>:0:<heading>:0
		// $FP<callsign>:*A:<flight plan type>:<aircraft type>:<true air speed>:<origin airport>:<departure time EST>:<departure time ACT>:<final cruising altitude>:<destination airport>:<HRS en route>:<MINS en route>:<HRS fuel>:<MINS fuel>:<alternate airport>:<remarks>:<route>
		$this->_scenario.="@N:$flight->callsign:".$this->_renderSquawk().":1:$flight->current_latitude:$flight->current_longitude:$flight->current_altitude:0:".$this->_calculateInitialHeading($flight->current_heading).":0\n";
		$this->_scenario.='$FP'."$flight->callsign:*A:$flight->flight_type:$flight->aircraft::$flight->departure_airport:".$flight->connected_at->format("Hi").":".$flight->connected_at->format("Hi").":$flight->cruise_altitude:$flight->arrival_airport:::::$flight->alternative_airport:$flight->remarks:$flight->route\n";
		$this->_scenario.='$ROUTE:FPA'."\n".'START:'.rand(0,1)."\n".'DELAY:'.rand(0,2).":".rand(2,5)."\nINITIALPSEUDOPILOT:$this->_initialPseudoPilot\n\n";
	}

	/**
	 * Grab some flightplans from our realtime tracking
	 * 
	 * @param  [type]  $icao [description]
	 * @param  boolean $from [description]
	 * @return [type]        [description]
	 */
	protected function _findFlightplans($icao, $from = true)
	{
		if($from) {
			// Find Departure Routes
			return \App\Models\Network\PilotClient::online()->where('departure_airport', $icao)->take($this->_departures)->get();
		} else {
			return \App\Models\Network\PilotClient::online()->where('arrival_airport', $icao)->take($this->_arrivals)->get();
		}
	}

	/**
	 * Calculate the initail heading to match EuroScope's screenspace
	 * 
	 * @param  [type] $hdg [description]
	 * @return [type]      [description]
	 */
	protected function _calculateInitialHeading($hdg)
	{
		return ((int) ( $hdg * 2.88 + 0.5 ) << 2 );
	}

	/**
	 * Find a squawk
	 * 
	 * @return [type] [description]
	 */
	protected function _renderSquawk()
	{
		$squawk = rand($this->_squawkRange['min'], $this->_squawkRange['max']);
		while(!$this->_isSquawkValid($squawk))
			$squawk = rand($this->_squawkRange['min'], $this->_squawkRange['max']);
		return $squawk;
	}

	/**
	 * Is a given 4 digit squawk code within valid range?
	 * So that any digit is less or equal to 7.
	 * 
	 * @param  [type]  $sq [description]
	 * @return boolean     [description]
	 */
	protected function _isSquawkValid($sq) {
		return preg_match("/^[0-7]{4}/", $sq) && $sq < 7778;
	}
}