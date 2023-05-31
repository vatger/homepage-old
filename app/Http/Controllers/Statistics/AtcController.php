<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Statistic\AtcData;
use Illuminate\Http\Request;

class AtcController extends Controller
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
        return $this->viewMake('frontend.statistics.atcSearch');
    }

    /**
     * Search for a resource.
     *
     * @param  int  $id
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
        $ad = null;

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
        if(preg_match('/[A-Z]\w*/', $validated['searchString'])) {
            $ad = AtcData::callsign($validated['searchString'])->whereBetween('connected_at', [$from, $till])->orderBy('connected_at', 'DESC')->get();
        }
        // Is it a cid?
        if(preg_match('/\[0-9\]{7}/', $validated['searchString']) || preg_match('/[0-9]{6}/', $validated['searchString'])) {
            $ad = AtcData::cid($validated['searchString'])->whereBetween('connected_at', [$from, $till])->orderBy('connected_at', 'DESC')->get();
        }
        // Is it an icao?
        if(preg_match('/1[1-9]{2}[.][0-9]{3}/', $validated['searchString'])) {
            $ad = AtcData::frequency($validated['searchString'])->whereBetween('connected_at', [$from, $till])->orderBy('connected_at', 'DESC')->get();
        }

        // dd($ad);

        if($validated){
            return $this->viewMake('frontend.statistics.atcSessions')->with('searched', $validated['searchString'])->with('atcSessions', $ad);
        }
        return redirect()->back()->withInput();
    }
}
