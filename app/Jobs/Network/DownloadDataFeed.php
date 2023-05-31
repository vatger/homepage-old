<?php

namespace App\Jobs\Network;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadDataFeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataFeedUrl = "https://data.vatsim.net/v3/vatsim-data.json";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        \Illuminate\Support\Facades\Cache::put('network.data.lastUpdate', Carbon::now()->toDateTimeString(), 59);

        $data = $this->_downloadDataFeedFile($this->dataFeedUrl);
        if(is_null($data)){
            return;
        }
        try {
            $this->_processData($data);
        } catch (Exception $e){
            //handle
        }
    }

    /**
     * Download the raw networkfeed file
     * @param string $url
     * @return string|null
     */
    private function _downloadDataFeedFile(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        if(curl_errno($ch)){
            $data = null;
        }
        curl_close($ch);
        return $data;
    }


    private function _processData($data)
    {
        $connectedClients = json_decode($data);
        \Illuminate\Support\Facades\Cache::put('network.data.connectedClients', $connectedClients, 300);
        \Log::channel('jobdaily')->info('[DownloadDataFeed]::Updating Pilots');

        if (is_null($connectedClients) || is_null($connectedClients->general) || is_null($connectedClients->general->update_timestamp)) {
            \Log::channel('jobdaily')->info('[DownloadDataFeed]::Datafeed invalid');
            return;
        }

        \App\Models\Network\PilotClient::whereNull('disconnected_at')
            ->update(['disconnected_at' => \Carbon\Carbon::parse($connectedClients->general->update_timestamp)->toDateTimeString()]);
        foreach ($connectedClients->pilots as $pilot) {
            $this->_updatePilot($pilot);
        }
        \Log::channel('jobdaily')->info('[DownloadDataFeed]::Pilots Updated');
        \App\Models\Network\AtcClient::whereNull('disconnected_at')
            ->update(['disconnected_at' => \Carbon\Carbon::parse($connectedClients->general->update_timestamp)->toDateTimeString()]);
        foreach ($connectedClients->controllers as $atc) {
            $this->_updateATC($atc);
        }
    }

    /**
     * Insert a pilot into table
     * @param  mixed $pilot
     * @return void
     */
    private function _updatePilot($pilot)
    {
        $p = $pilot;
        if (\is_null($p) || \is_null($p->flight_plan)) {
            return;
        }

        // delete ONLY multiple flight, CAVE there may be multiple flights in one VATSIM connection, which is ok
        $sameFlightCount = \App\Models\Network\PilotClient::where('callsign', $p->callsign)
            ->where('departure_airport', 'LIKE', $p->flight_plan->departure)
            ->where('arrival_airport', 'LIKE', $p->flight_plan->arrival)
            ->count();
        $firstSameFlight = \App\Models\Network\PilotClient::where('callsign', $p->callsign)
            ->where('departure_airport', 'LIKE', $p->flight_plan->departure)
            ->where('arrival_airport', 'LIKE', $p->flight_plan->arrival)
            ->first();
        if ($sameFlightCount > 1) {
            $dbPilots = \App\Models\Network\PilotClient::where('callsign', $p->callsign)
                ->where('departure_airport', 'LIKE', $p->flight_plan->departure)
                ->where('arrival_airport', 'LIKE', $p->flight_plan->arrival)
                ->where('id', '!=', $firstSameFlight->id)
                ->delete();
        }

        $pilotClient = \App\Models\Network\PilotClient::firstOrNew(
            [
                'account_id' => $p->cid,
                'callsign' => $p->callsign,
                'departure_airport' => $p->flight_plan->departure,
                'arrival_airport' => $p->flight_plan->arrival,
                'alternative_airport' => $p->flight_plan->alternate,
            ]
        );
        if(empty($pilotClient->connected_at)) $pilotClient->connected_at = \Carbon\Carbon::parse($p->logon_time)->toDateTimeString();
        $pilotClient->minutes_online = \Carbon\Carbon::parse($pilotClient->connected_at)->diffInMinutes($p->last_updated);
        $pilotClient->disconnected_at = null;
        $pilotClient->aircraft = $p->flight_plan->aircraft_short; //just B738 not B738/L or B738/M-SDE2E3FGHIJ1RWY/LB1
        $pilotClient->cruise_altitude = $p->flight_plan->altitude;
        $pilotClient->cruise_tas = $p->flight_plan->cruise_tas;
        $pilotClient->flight_type = $p->flight_plan->flight_rules;
        $pilotClient->route = $p->flight_plan->route;
        $pilotClient->remarks = $p->flight_plan->remarks;
        $pilotClient->current_latitude = $p->latitude;
        $pilotClient->current_longitude = $p->longitude;
        $pilotClient->current_altitude = $p->altitude;
        $pilotClient->current_groundspeed = $p->groundspeed;
        $pilotClient->current_heading = $p->heading;
        // $pilotClient->save(); // Fill be saved in the subfunction.
        $this->_postUpdatePilot($pilotClient, $p);
    }

    private function _postUpdatePilot($pilotClient, $p)
    {
        // Find out if the pilot has departed and arrived
        $depAirport = \App\Models\Navigation\Aerodrome::icao($pilotClient->departure_airport)->first();
        if ($depAirport != null && $pilotClient->departed_at == null) {
            // we actually know the airport
            // and the pilot is not departed by now
            if (!$depAirport->containsCoordinates($pilotClient->current_latitude, $pilotClient->current_longitude)) {
                // Pilot is no longer in the vicinity of the airport
                if ($pilotClient->current_altitude > $depAirport->elevation + 300 && $pilotClient->current_groundspeed > 35) {
                    $pilotClient->departed_at = \Carbon\Carbon::parse($p->last_updated)->toDateTimeString();
                }
            }
        } else {
            if ($pilotClient->departed_at == null) {
                if ($pilotClient->current_groundspeed > 35) {
                    $pilotClient->departed_at = \Carbon\Carbon::parse($p->last_updated)->toDateTimeString();
                }
            }
        }
        $arrAirport = \App\Models\Navigation\Aerodrome::icao($pilotClient->arrival_airport)->first();
        if ($arrAirport != null && $pilotClient->arrived_at == null) {
            if ($arrAirport->containsCoordinates($pilotClient->current_latitude, $pilotClient->current_longitude)) {
                // At least within airport boundary...
                if ($pilotClient->current_altitude < $arrAirport->elevation + 100 && $pilotClient->current_groundspeed <= 35) {
                    $pilotClient->arrived_at = \Carbon\Carbon::parse($p->last_updated)->toDateTimeString();
                }
            }
        } else {
            if ($pilotClient->arrived_at == null) {
                if ($pilotClient->current_groundspeed <= 35) {
                    $pilotClient->arrived_at = \Carbon\Carbon::parse($p->last_updated)->toDateTimeString();
                }
            }
        }
        $pilotClient->save();
    }

    /**
     * Insert an ATC into table
     * @param  mixed $atc
     * @return void
     */
    private function _updateATC($atc)
    {
        $a = $atc;
        if ($a->facility == 0 || $a->frequency == "199.998" || preg_match('/_OBS/s', $a->callsign) || preg_match('/_ATIS/s', $a->callsign)) {
            return;
        }

        $atcClient = \App\Models\Network\AtcClient::firstOrNew(
            [
                'account_id' => $a->cid,
                'callsign' => $a->callsign,
            ]
        );
        $atcClient->connected_at = \Carbon\Carbon::parse($a->logon_time)->toDateTimeString();
        $atcClient->minutes_online = \Carbon\Carbon::parse($a->logon_time)->diffInMinutes($a->last_updated);

        $atcClient->disconnected_at = null;
        $atcClient->frequency = $a->frequency;
        $atcClient->qualification_id = $a->rating;
        $atcClient->facility_type = $a->facility;
        $atcClient->save();
    }
}
