<?php

namespace App\Jobs\Membership;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Membership\Account;

class CleanIncompletedRegistrationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $_accounts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($accounts)
    {
        $this->_accounts = $accounts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (count($this->_accounts) == 0) return;

        foreach ($this->_accounts as $acc) {
            $acc->loadMissing('data', 'setting');

            if ($acc->setting != null) $acc->setting->forceDelete();
            if ($acc->data != null) $acc->data->forceDelete();
            $acc->forceDelete();
        }

    }
}
