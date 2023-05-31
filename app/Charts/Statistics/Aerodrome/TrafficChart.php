<?php

declare(strict_types = 1);

namespace App\Charts\Statistics\Aerodrome;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;

class TrafficChart extends BaseChart
{

    private $_arrivals;
    private $_departures;

    private $_start;
    private $_end;

    private $_labels = [];
    private $_arrivalsPerHour = [];
    private $_departuresPerHour = [];

    function __construct(){
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
            $this->_arrivalsPerHour[] = $this->_arrivals->filter(
                function ($arrival) use ($s, $e) {
                    return $arrival->arrived_at >= $s
                    && $arrival->arrived_at < $e;
                }
            )->count();
            $this->_departuresPerHour[] = $this->_departures->filter(
                function ($departure) use ($s, $e) {
                    return $departure->departed_at >= $s
                    && $departure->departed_at < $e;
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

        set_time_limit(0); // Run as long as needed. Will not effect page load due to ajax implementation

        $from = \Carbon\Carbon::createFromFormat('d.m.Y', $request->from, 'utc');
        $from->setHours(0);
        $from->setMinutes(0);
        $from->setSeconds(0);
        
        $till = \Carbon\Carbon::createFromFormat('d.m.Y', $request->till, 'utc');
        $till->setHours(23);
        $till->setMinutes(59);
        $till->setSeconds(59);

        $this->_start = $from;
        $this->_end = $till;

        $this->renderLabels();

        $aerodrome = \App\Models\Navigation\Aerodrome::icao($request->aerodrome)->firstOrFail();

        $departure = \App\Models\Network\PilotClient::where('departure_airport', $aerodrome->icao)
                    ->whereBetween('departed_at', [$from, $till])
                    ->orderBy('departed_at', 'DESC')
                    ->get();
        
        $arrival = \App\Models\Network\PilotClient::where('arrival_airport', $aerodrome->icao)
                    ->whereBetween('arrived_at', [$from, $till])
                    ->orderBy('arrived_at', 'DESC')
                    ->get();

        $this->_arrivals = \App\Models\Statistic\FlightData::where('arrival_airport', $aerodrome->icao)
                    ->whereBetween('arrived_at', [$from, $till])
                    ->orderBy('arrived_at', 'DESC')
                    ->get();
        $this->_departures = \App\Models\Statistic\FlightData::where('departure_airport', $aerodrome->icao)
                    ->whereBetween('departed_at', [$from, $till])
                    ->orderBy('departed_at', 'DESC')
                    ->get();

        $this->_arrivals = $this->_arrivals->merge($arrival);
        $this->_departures = $this->_departures->merge($departure);

        $this->calculateMovements();

        return Chartisan::build()
            ->labels($this->_labels)
            ->dataset('Arrivals', $this->_arrivalsPerHour)
            ->dataset('Departures', $this->_departuresPerHour);
    }
}