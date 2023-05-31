<?php

declare(strict_types = 1);

namespace App\Charts\Statistics\Flightdata;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\Statistic\FlightData;

class ActivityChart extends BaseChart
{
    private $_flights;

    private $_start;
    private $_end;

    private $_labels = [];
    private $_flightsPerHour = [];

    function __construct(){
        
        $this->_end = \Carbon\Carbon::now()->utc();
        $this->_start = $this->_end->copy()->subDays(14);
        $this->_start->setTime(0, 0, 0, 0);
        $this->_end->setTime(23, 59, 59, 999);

        $this->renderLabels();
    }

    private function renderLabels()
    {
        $timespan = $this->_start->diffInDays($this->_end) + 1;
        $hourCounter = 0;
        $timeframe = $timespan * 24; // Days in hours
        $immDate = $this->_start->copy()->toImmutable();
        while ($hourCounter < $timeframe) {
            $s = $immDate->addHours($hourCounter);
            $e = $immDate->addHours($hourCounter + 1);
            if (0 == $hourCounter) {
                $labels[0] = $this->_start->format('d.m.Y H:i');
            } elseif ($hourCounter == $timeframe) {
                $this->_labels[] = $this->_end->format('d.m.Y H:i');
            } else {
                $this->_labels[] = $s->format('d.m.Y H:i');
            }
            $hourCounter++;
        }
    }

    private function calculateMovements()
    {
        $timespan = $this->_start->diffInDays($this->_end) + 1;
        $hourCounter = 0;
        $timeframe = $timespan * 24; // Days in hours
        $immDate = $this->_start->copy()->toImmutable();
        while ($hourCounter < $timeframe) {
            $s = $immDate->addHours($hourCounter);
            $e = $immDate->addHours($hourCounter + 1);
            $this->_flightsPerHour[] = $this->_flights->filter(
                function ($f) use ($s, $e) {
                    return $f->connected_at >= $s
                    && $f->connected_at < $e;
                }
            )->count();
            $hourCounter++;
        }
    }

    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $validated = $request->validate(
            [
                'searchString' => 'string|required'
            ]
        );

        // Do the search
        $fd = null;
        // Is it a callsign?
        if(preg_match('/[A-Z]{3}\w*/', $validated['searchString'])) {
            $fd = FlightData::callsign($validated['searchString'])->orderBy('connected_at', 'DESC')->get();
        }
        // Is it an icao?
        if(preg_match('/[a-zA-Z]{4}/', $validated['searchString'])) {
            $fd = FlightData::icao($validated['searchString'])->orderBy('connected_at', 'DESC')->get();
        }
        // Is it a cid?
        if(preg_match('/\[0-9\]{7}/', $validated['searchString']) || preg_match('/[0-9]{6}/', $validated['searchString'])) {
            $fd = FlightData::cid($validated['searchString'])->orderBy('connected_at', 'DESC')->get();
        }

        if($fd != null) {
            $this->_flights = $fd->filter(function($flight) {
                if($flight->departure_airport != '' && $flight->arrival_airport != '') return true;
                return false;
            });
        }

        $this->calculateMovements();

        return Chartisan::build()
            ->labels($this->_labels)
            ->dataset('Flights', $this->_flightsPerHour);
    }
}