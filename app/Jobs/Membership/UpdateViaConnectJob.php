<?php

namespace App\Jobs\Membership;

use App\Helpers\ConnectHelper;
use App\Models\Membership\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class UpdateViaConnectJob implements ShouldQueue
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
        if (Cache::has('membership.updater.connect.lastUpdatedAccount')) {
            $this->_lastUpdatedAccount = Cache::get('membership.updater.connect.lastUpdatedAccount');
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
        \Log::channel('jobdaily')->info('[Connect]::Starting update. Starting CID ' . $this->_lastUpdatedAccount);

        $chunkSize = $this->_totalAccounts > 100 ? ceil($this->_totalAccounts / (24*7)) : 50; // all accounts in one week with jobs every hour

        $accountsToUpdate = Account::where('id', '>=', $this->_lastUpdatedAccount + 1)->where('refresh_token', '!=', null)->take($chunkSize)->get();
        foreach ($accountsToUpdate as $acc) {
            ConnectHelper::updateAccount($acc);
        }
        if ($accountsToUpdate->count() < $chunkSize) {
            Cache::forget('membership.updater.connect.lastUpdatedAccount');
            \Log::channel('jobdaily')->info('[Connect]::Finished update. Last CID ' . $accountsToUpdate->last()->id);
        } else {
            Cache::put('membership.updater.connect.lastUpdatedAccount', $accountsToUpdate->last()->id);
            \Log::channel('jobdaily')->info('[Connect]::Stopped update. Last CID ' . $accountsToUpdate->last()->id);
        }

    }
}
