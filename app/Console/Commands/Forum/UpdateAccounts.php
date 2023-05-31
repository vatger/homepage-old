<?php

namespace App\Console\Commands\Forum;

use Illuminate\Console\Command;

class UpdateAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forum:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all application accounts with the forum';

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
        \App\Jobs\Forum\UpdateAccounts::dispatch();
    }
}
