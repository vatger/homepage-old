<?php

namespace App\Http\Controllers\Network;

use App\Http\Controllers\Controller;
use App\Models\Membership\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DatafeedApiController extends Controller
{

	public function getLocalAtc(Request $request, $dashboard = false)
	{
		if($request->ajax()) {
            
			if($dashboard && Auth::check()) {
                return \App\Models\Network\AtcClient::online()->isDe()->orderBy('connected_at', 'ASC')->with('account')->get();
			} else {
				return \App\Models\Network\AtcClient::online()->isDe()->orderBy('connected_at', 'ASC')->get();
			}

		}
		abort(403);
	}

	public function getOnlineAtc(Request $request)
	{
		if($request->ajax()) {
			return \App\Models\Network\AtcClient::online()->orderBy('connected_at', 'ASC')->get();
		}
		abort(403);
	}

	public function getActiveFlights(Request $request)
	{
		if($request->ajax()){
			return \App\Models\Network\PilotClient::online()->orderBy('connected_at', 'ASC')->get();
		}
		abort(403);
	}

	public function getConnectedClients(Request $request)
	{
		// $connectedClients = Cache at 'network.data.connectedClients'
		$connectedClients = \Illuminate\Support\Facades\Cache::get('network.data.connectedClients');
		
		return json_encode($connectedClients, JSON_PRETTY_PRINT);
	}


	public function getWeather(Request $request, $icao)
	{
		/**
		 * We might have multiple records grab
		 */
		if(strlen($icao) > 4) {
			$icao = str_replace(' ', '', $icao); // Get rid of any whitespace
			$icaoSplit = explode(',', $icao);
			$metars = [];
			foreach ($icaoSplit as $i) {
				if(strlen($i) == 4)
					$metars[$i] = $this->getWeather($request, $i);
			}
			return $metars;
		}

		/**
		 * We only have a 4 char long icao here ?!
		 */
		$icaoCode = strtoupper($icao);
		if(preg_match('/^[A-Z]{4}$/', $icaoCode) == 1) {
			// we have an uppercase 4 char long icao...
			$metar = \Illuminate\Support\Facades\Cache::remember(
				'network.metar.'.$icaoCode,
				5 * 60,
				function () use ($icaoCode) {
					$client = new \GuzzleHttp\Client([
						'base_uri' => 'https://metar.vatsim.net/',
					]);

					try {
						$response = $client->get(
							'metar.php',
							[
								'query' => [
									'id' => $icaoCode
								],
							]
						);
						if($response->getStatusCode() == 200) {
							if(0 === strpos((string) $response->getBody(), $icaoCode)) {
								return (string) $response->getBody();
							} else {
								return null;
							}
						}
					} catch (\GuzzleHttp\Exception\TransferException $e) {
					}
					return null;
				}
			);
			return $metar ?? 'Currently the automated weather system is offline.';
		} else {
			return 'No weather available for '.$icaoCode;
		}
	}

}
