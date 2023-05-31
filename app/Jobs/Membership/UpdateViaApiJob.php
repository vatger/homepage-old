<?php

namespace App\Jobs\Membership;

use App\Helpers\ApiHelper;
use App\Models\Membership\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class UpdateViaApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $_lastUpdatedAccount = null;

    private $_totalAccounts = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Cache::has('membership.updater.api.lastUpdatedAccount')) {
            $this->_lastUpdatedAccount = Cache::get('membership.updater.api.lastUpdatedAccount');
        } else {
            $this->_lastUpdatedAccount = 0;
        }

        $this->_totalAccounts = Account::count();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::channel('jobdaily')->info('[Api]::Starting update Api. Starting CID ' . $this->_lastUpdatedAccount);

        $chunkSize = $this->_totalAccounts > 100 ? ceil($this->_totalAccounts / (7*24)) : 50; // all accounts in 1/2 week with jobs every half hour

        $accountsToUpdate = Account::where('id', '>=', $this->_lastUpdatedAccount + 1)->take($chunkSize)->get();
        foreach ($accountsToUpdate as $acc) {
            ApiHelper::updateAccount($acc);
        }
        if ($accountsToUpdate->count() < $chunkSize) {
            Cache::forget('membership.updater.api.lastUpdatedAccount');
            \Log::channel('jobdaily')->info('[Api]::Finished update. Last CID ' . $accountsToUpdate->last()->id);
        } else {
            Cache::put('membership.updater.api.lastUpdatedAccount', $accountsToUpdate->last()->id);
            \Log::channel('jobdaily')->info('[Api]::Stopped update. Last CID ' . $accountsToUpdate->last()->id);
        }

    }
}
