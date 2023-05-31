<?php

namespace App\Jobs\Statistic;

use App\Models\Statistic\AtcData;
use App\Models\Statistic\FlightData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearStatistics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // disable for now
        //\Log::channel('jobdaily')->info('[Statistics]::Clearing DB');
        //$this->_handleAtcClients();
        //$this->_handlePilotClients();
        //\Log::channel('jobdaily')->info('[Statistics]::Cleared DB');
    }

    private function _handleAtcClients()
    {
        // TODO: Find whats needed to be checked here and then clear stats accordingly
        AtcData::query()
            ->whereNotNull('disconnected_at') //the client has disconnected
            ->where('minutes_online', '<',  5)->delete();
    }

    private function _handlePilotClients()
    {
        FlightData::query()
            ->whereNotNull('disconnected_at') //the client has disconnected
            ->where('minutes_online', '<',  5)->delete();

        FlightData::query()
            ->whereNotNull('disconnected_at') //the client has disconnected
            ->where('arrival_airport', 'LIKE',  '')
            ->where('departure_airport', 'LIKE',  '')->delete();
    }
}
