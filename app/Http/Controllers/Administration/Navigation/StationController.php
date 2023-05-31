<?php

namespace App\Http\Controllers\Administration\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Navigation\Station;
use App\Notifications\Administration\Navigation\StationUpdatedNotification;
use App\Notifications\Administration\Navigation\StationCreatedNotification;
use App\Notifications\Administration\Navigation\StationRemovedNotification;

class StationController extends Controller
{
    
	function __construct()
	{
		parent::__construct();

		$this->middleware(function ($request, $next) {

			if(!$this->account->hasPermission('administration.navigation'))
				abort(403);

			return $next($request);
		});
	}

	/**
	 * Get stations
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function index(Request $request)
	{
		if($request->ajax()) {
			return Station::orderBy('ident', 'ASC')->get();
		}
		abort(403);
	}

	/**
	 * Updates a station
	 * 
	 * @param  Request $request [description]
	 * @param  Station $station [description]
	 * @return [type]           [description]
	 */
	public function updateStation(Request $request, Station $station)
	{
		if($request->ajax()) {
			$validated = $request->validate([
				'station.name' => 'required|string',
				'station.ident' => 'required|string',
				'station.frequency' => 'required|numeric',
				'station.atis' => 'required|boolean',
				'station.bookable' => 'required|boolean',
			]);

			$validator = Validator::make($request->all(), [
				'station.frequency' => [
					'required',
					'numeric',
					function($attribute, $value, $fail) {
						if(preg_match("/(\d{4,}(\.|,)|(\.|,)\d{4,})/", $value)) {
							$fail('The frequency must have 3 digits before and not more than 3 digits behind the dot.');
						}
					}
				]
			])->validate();

			$station->name = $validated['station']['name'];
			$station->ident = $validated['station']['ident'];
			$station->frequency = floatval($validated['station']['frequency']);
			$station->atis = boolval($validated['station']['atis']);
			$station->bookable = boolval($validated['station']['bookable']);

			$station->save();

			$this->account->notify(new StationUpdatedNotification($station));

			return $station;
		}
		abort(403);
	}

	/**
	 * Creates a station
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function createStation(Request $request)
	{
		if($request->ajax()) {
			$validated = $request->validate([
				'station.name' => 'required|string',
				'station.ident' => 'required|string',
				'station.frequency' => 'required|numeric',
				'station.atis' => 'required|boolean',
				'station.bookable' => 'required|boolean',
			]);

			$validator = Validator::make($request->all(), [
				'station.frequency' => [
					'required',
					'numeric',
					function($attribute, $value, $fail) {
						if(preg_match("/(\d{4,}(\.|,)|(\.|,)\d{4,})/", $value)) {
							$fail('The frequency must have 3 digits before and not more than 3 digits behind the dot.');
						}
					}
				]
			])->validate();

			$station = new Station();
			$station->name = $validated['station']['name'];
			$station->ident = $validated['station']['ident'];
			$station->frequency = floatval($validated['station']['frequency']);
			$station->atis = boolval($validated['station']['atis']);
			$station->bookable = boolval($validated['station']['bookable']);

			$station->save();

			$this->account->notify(new StationCreatedNotification($station));
			
			return $station;
		}
		abort(403);
	}

	/**
	 * Deletes a station
	 * 
	 * @param  Request $request [description]
	 * @param  Station $station [description]
	 * @return [type]           [description]
	 */
	public function deleteStation(Request $request, Station $station)
	{
		if($request->ajax()) {
			$station->aerodromes()->detach();

			if($station->bookable) {
				// Find all bookings for that station and remove those
				$bookings = \App\Models\Booking\AtcSessionBooking::forStation($station->id)->get();
				foreach ($bookings as $b) {
					// TODO: When vatbook hook is implemented...
					// Also remove vatbook entry from here
					$b->delete();
				}
			}
			$ident = $station->ident;
			$station->delete();
			$this->account->notify(new StationRemovedNotification($ident));

			return $ident;
		}
		abort(403);
	}

}
