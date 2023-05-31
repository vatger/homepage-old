<?php

namespace App\Jobs\Teamspeak;

use App\Libraries\TeamSpeakWebquery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class UpdateViaWebQuery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $_lastUpdatedListNr = null;
    private $_clientDBlist = [];
    private $_chunckNumber = 25;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Cache::has('teamspeak.updater.lastUpdatedListNr')) {
            $this->_lastUpdatedListNr = Cache::get('teamspeak.updater.lastUpdatedListNr');
        } else {
            $this->_lastUpdatedListNr = 0;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::channel('jobdaily')->info('[TS]::Starting update. First ID ' . $this->_lastUpdatedListNr);

        for ($i = 0; $i < $this->_chunckNumber; $i++) {
            $this->_clientDBlist = TeamSpeakWebquery::getClientDB($this->_lastUpdatedListNr);
            if (count($this->_clientDBlist) == 0) {
                Cache::put('teamspeak.updater.lastUpdatedListNr', 0);
                \Log::info('[TS]::Finished update. Checked ' . $this->_lastUpdatedListNr);
                $this->_lastUpdatedListNr = 0;
                return;
            }
            foreach ($this->_clientDBlist as $client) {
                TeamSpeakWebquery::checkClient($client);
                $this->_lastUpdatedListNr++;
            }
        }
        Cache::put('teamspeak.updater.lastUpdatedListNr', $this->_lastUpdatedListNr);

        \Log::channel('jobdaily')->info('[TS]::Stopped update. Last ID ' . ($this->_lastUpdatedListNr - 1));
    }
}
