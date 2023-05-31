<?php

namespace App\Console\Commands\Account;

use Illuminate\Console\Command;
use App\Models\Membership\Account;

class ClearUncompletedRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:clear-unfinished-registrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will remove any unfinished registrations from the database.';

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
        $accounts = Account::where('setup_completed', false)->where('created_at', '<', \Carbon\Carbon::now()->subMinutes(30))->get();
        \App\Jobs\Membership\CleanIncompletedRegistrationsJob::dispatch($accounts);
    }
}
