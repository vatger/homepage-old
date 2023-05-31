<?php

namespace App\Jobs\Statistic;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateStatistics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $_atcClients;

    private $_pilotClients;

    private $_timestamp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        \Log::channel('jobdaily')->info('[Statistics]::Initializing updater');
        $this->_timestamp = \Carbon\Carbon::now()->utc();
        //$this->_atcClients = \App\Models\Network\AtcClient::all();
        //$this->_pilotClients = \App\Models\Network\PilotClient::all();
    }

    /**
     * Update ATC statistics table and remove client from live table if session is completed
     * @return void
     */
    private function _handleAtcClients()
    {
        foreach (\App\Models\Network\AtcClient::query()->limit(500)->cursor() as $atc) {
            // If a session is completed, move it to the statistics table
            if(!is_null($atc->disconnected_at)) {
                // Okay, session completed.
                // Let's find any session in the statistics that might be related to this.
                $related = \App\Models\Statistic\AtcData::where('account_id', $atc->account_id)
                    ->whereBetween('connected_at', [$atc->connected_at->subMinutes(5), $this->_timestamp])
                    ->where('callsign', $atc->callsign)
                    ->first();
                if($related) {
                    // Might have a related session here.
                    // Let's just update the and disregard the new one
                    $related->disconnected_at = $atc->disconnected_at;
                    $related->minutes_online = \Carbon\Carbon::parse($related->connected_at)->diffInMinutes($related->disconnected_at, true);
                    $related->save();
                    $atc->delete();
                    continue;
                } else {
                    // No related session found.
                    // Copy this one over
                    $atcStat = new \App\Models\Statistic\AtcData($atc->toArray());
                    $atcStat->save();
                    $atc->delete();
                    continue;
                }
            } else {
                // Clean up
                if($this->_timestamp->diffInMinutes($atc->connected_at) > (14 * 60)) {
                    $atc->delete(); // 14hrs of atc? Clean that garbage
                    // If the client is still online it will be readded with the next cycle
                }
            }
        }
    }

    /**
     * Update Pilot statistics table and remove completed flights from live table
     * @return [type] [description]
     */
    private function _handlePilotClients()
    {
        foreach (\App\Models\Network\PilotClient::query()->limit(5000)->cursor() as $pilot) {
            if(!is_null($pilot->disconnected_at)) {
                // Completed flight.
                // Find a related flight by the account within a given timeframe with same route
                $related = \App\Models\Statistic\FlightData::where('account_id', $pilot->account_id)
                    ->whereBetween('connected_at', [$pilot->connected_at->subMinutes(60), $this->_timestamp])
                    ->where('callsign', $pilot->callsign)
                    ->where('departure_airport', $pilot->departure_airport)
                    ->where('arrival_airport', $pilot->arrival_airport)
                    ->first();
                if($related != null) {
                    $related->flight_type = $pilot->flight_type;
                    $related->route = $pilot->route;
                    $related->remarks = $pilot->remarks;
                    //$related->current_latitude = $pilot->current_latitude;
                    //$related->current_longitude = $pilot->current_longitude;
                    //$related->current_altitude = $pilot->current_altitude;
                    //$related->current_groundspeed = $pilot->current_groundspeed;
                    //$related->current_heading = $pilot->current_heading;
                    if (is_null($related->departed_at)){
                        $related->departed_at = $pilot->departed_at;
                    }
                    $related->arrived_at = $pilot->arrived_at;

                    $related->disconnected_at = $pilot->disconnected_at;
                    $related->minutes_online = \Carbon\Carbon::parse($related->connected_at)->diffInMinutes($related->disconnected_at, true);
                    $related->save();
                    //\Log::info($pilot->callsign . ' deleted due to related flight.');
                    $pilot->delete();
                    continue;
                } else {
                    // Finished flight
                    // Copy to stats
                    $pilotStat = new \App\Models\Statistic\FlightData($pilot->toArray());

                    $pilotStat->departed_at = $pilot->departed_at;
                    $pilotStat->arrived_at = $pilot->arrived_at;

                    if (!is_null($pilotStat->departed_at) && !is_null($pilotStat->arrived_at)) {
                        //$pilotStat->minutes_inair = \Carbon\Carbon::parse($pilot->departed_at)->diffInMinutes($pilot->arrived_at, true);
                    }
                    $pilotStat->save();
                    //\Log::info($pilot->callsign . ' deleted due to completed flight cycle.');
                    $pilot->delete();
                    continue;
                }
            } else {
                // Clean up forgotten flights
                if($this->_timestamp->diffInMinutes($pilot->connected_at) > (24 * 60)) {
                    //\Log::info($pilot->callsign . ' deleted due to exessive online period.');
                    $pilot->delete(); // If the flight has not been completed within this timeframe, remove it

                    // If the client is still online it will be readded with the next cycle
                }
            }
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::channel('jobdaily')->info('[Statistics]::Updating DB');
        $this->_handleAtcClients();

        $this->_handlePilotClients();
        \Log::channel('jobdaily')->info('[Statistics]::Updated DB');

        //\App\Jobs\Statistic\ClearStatistics::dispatch(); // disable for now
    }
}
