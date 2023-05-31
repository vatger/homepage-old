<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Navigation\Aerodrome;

class AerodromeController extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->viewMake('frontend.statistics.aerodromes');
    }

    /**
     * Display the specified resource.
     *
     * @param  Aerodrome  $aerodrome
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Aerodrome $aerodrome)
    {
        $from = \Carbon\Carbon::createFromFormat('d.m.Y', $request->from, 'utc');
        $from->setHours(0);
        $from->setMinutes(0);
        $from->setSeconds(0);
        
        $till = \Carbon\Carbon::createFromFormat('d.m.Y', $request->till, 'utc');
        $till->setHours(23);
        $till->setMinutes(59);
        $till->setSeconds(59);
        // Find data for an aerodrome
        $atcHistory = \App\Models\Network\AtcClient::offline()
                    ->icao($aerodrome->icao)
                    ->whereBetween('connected_at', [$from, $till])
                    ->orderBy('connected_at', 'DESC')
                    ->get();
        $atcCurrent = \App\Models\Network\AtcClient::online()
                    ->icao($aerodrome->icao)
                    // ->whereBetween('connected_at', [$from, $till])
                    ->orderBy('connected_at', 'DESC')
                    ->get();

        $departureHistory = \App\Models\Network\PilotClient::offline()
                    ->where('departure_airport', $aerodrome->icao)
                    ->whereBetween('departed_at', [$from, $till])
                    ->orderBy('departed_at', 'DESC')
                    ->get();
        $departureStats = \App\Models\Statistic\FlightData::where('departure_airport', $aerodrome->icao)
                    ->whereBetween('departed_at', [$from, $till])
                    ->orderBy('departed_at', 'DESC')
                    ->get();
        $departureHistory = $departureHistory->merge($departureStats);

        $departureCurrent = \App\Models\Network\PilotClient::online()
                    ->where('departure_airport', $aerodrome->icao)
                    // ->whereBetween('connected_at', [$from, $till])
                    ->orderBy('connected_at', 'DESC')
                    ->get();
        
        $arrivalHistory = \App\Models\Network\PilotClient::offline()
                    ->where('arrival_airport', $aerodrome->icao)
                    ->whereBetween('arrived_at', [$from, $till])
                    ->orderBy('arrived_at', 'DESC')
                    ->get();
        $arrivalStats = \App\Models\Statistic\FlightData::where('arrival_airport', $aerodrome->icao)
                    ->whereBetween('arrived_at', [$from, $till])
                    ->orderBy('arrived_at', 'DESC')
                    ->get();
        $arrivalHistory = $arrivalHistory->merge($arrivalStats);

        $arrivalCurrent = \App\Models\Network\PilotClient::online()
                    ->where('arrival_airport', $aerodrome->icao)
                    // ->whereBetween('connected_at', [$from, $till])
                    ->orderBy('connected_at', 'DESC')
                    ->get();

        // Find the statistics
        $atcStatistics = \App\Models\Statistic\AtcData::callsign($aerodrome->icao)
                    ->whereBetween('connected_at', [$from, $till])
                    ->orderBy('connected_at', 'DESC')
                    ->get();

        

        // Merge atc offline and atc statistics
        $atcHistory = $atcHistory->merge($atcStatistics);

        // Return the stats view
        return $this->viewMake('frontend.statistics.aerodrome')
            ->with('aerodrome', $aerodrome)
            ->with('atcHistory', $atcHistory)
            ->with('atcCurrent', $atcCurrent)
            ->with('departureHistory', $departureHistory)
            ->with('departureCurrent', $departureCurrent)
            ->with('arrivalHistory', $arrivalHistory)
            ->with('arrivalCurrent', $arrivalCurrent)
            ->with('from', $from)
            ->with('till', $till);
    }

    /**
     * Search an aerodrome by icao
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'searchString' => 'required|string'
        ]);

        $aerodrome = null;
        // Is it an icao?
        if(preg_match('/[a-zA-Z]{4}/', $validated['searchString'])) {
            $aerodrome = Aerodrome::icao($validated['searchString'])->first();
        }
        // City or name?
        if($aerodrome == null && preg_match('/[\w\s]+/', $validated['searchString'])) {
            $aerodrome = Aerodrome::where('name', $validated['searchString'])->orWhere('city', $validated['searchString'])->first();
        }

        if($aerodrome != null) {
            return redirect()->route('statistics.aerodrome.icao', $aerodrome->icao);
        } else {
            return redirect()->route('statistics.aerodrome.home');
        }

        abort(404);
    }
}
