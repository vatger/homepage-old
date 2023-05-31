<?php

namespace App\Console\Commands\Account;

use App\Helpers\ConnectHelper;
use App\Models\Membership\Account;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateApiAccounts extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:apiupdater';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command updates users data from Api.';


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
        \App\Jobs\Membership\UpdateViaApiJob::dispatch();
    }
}
