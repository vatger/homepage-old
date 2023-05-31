<?php

namespace App\Console\Commands\TeamSpeak;

use Illuminate\Console\Command;

class TeamSpeakWebqueryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ts:webquery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script to check TeamSpeak bans, unbans, client descriptions.';

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
     * @return int
     */
    public function handle()
    {
        \App\Jobs\Teamspeak\UpdateViaWebQuery::dispatch();
    }

}
