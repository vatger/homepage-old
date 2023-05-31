<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Statistic\FlightData;

class FlightController extends Controller
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
        return $this->viewMake('frontend.statistics.flight');
    }

    /**
     * Search stuff.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $validated = $request->validate(
            [
                'searchString' => 'string|required',
                'from' => 'required|date',
                'till' => 'required|date',
            ]
        );

        // Do the search
        $fd = null;

        try{
            $from = \Carbon\Carbon::createFromFormat('d.m.Y', $request->from, 'utc');
        } catch(\Carbon\Exceptions\InvalidFormatException $e) {
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->from, 'utc');
        }
        $from->setHours(0);
        $from->setMinutes(0);
        $from->setSeconds(0);
        
        try {
            $till = \Carbon\Carbon::createFromFormat('d.m.Y', $request->till, 'utc');
        } catch(\Carbon\Exceptions\InvalidFormatException $e) {
            $till = \Carbon\Carbon::createFromFormat('Y-m-d', $request->till, 'utc');
        }
        $till->setHours(23);
        $till->setMinutes(59);
        $till->setSeconds(59);
        
        // Is it a callsign?
        if(preg_match('/[A-Z]{3}\w*/', $validated['searchString'])) {
            $fd = FlightData::callsign($validated['searchString'])->whereBetween('connected_at', [$from, $till])->orderBy('connected_at', 'DESC')->get();
        }
        // Is it an icao?
        if(preg_match('/[a-zA-Z]{4}/', $validated['searchString'])) {
            $fd = FlightData::icao($validated['searchString'])->whereBetween('connected_at', [$from, $till])->orderBy('connected_at', 'DESC')->get();
        }
        // Is it a cid?
        if(preg_match('/\[0-9\]{7}/', $validated['searchString']) || preg_match('/[0-9]{6}/', $validated['searchString'])) {
            $fd = FlightData::cid($validated['searchString'])->whereBetween('connected_at', [$from, $till])->orderBy('connected_at', 'DESC')->get();
        }

        if($fd != null)
            $filteredFd = $fd->filter(function($flight) {
                if($flight->departure_airport != '' && $flight->arrival_airport != '') return true;
                return false;
            });
        else
            $filteredFd = array();

        return $this->viewMake('frontend.statistics.flights')->with('flightData', $filteredFd)->with('searchString', $validated['searchString']);
    }
}
