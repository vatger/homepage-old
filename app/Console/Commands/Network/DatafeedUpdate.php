<?php

namespace App\Console\Commands\Network;

use Illuminate\Console\Command;

class DatafeedUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:datafeed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the network datafeed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \App\Jobs\Network\DownloadDataFeed::dispatch();
    }
}
