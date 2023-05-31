<?php

namespace App\Jobs\Forum;

use App\Libraries\Gitlab;
use App\Libraries\XenBridge;
use App\Models\Membership\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Log;
use Throwable;

class UpdateAccounts implements ShouldQueue
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
        if (Cache::has("forum.updater.lastUpdatedAccount")) {
            $this->_lastUpdatedAccount = Cache::get(
                "forum.updater.lastUpdatedAccount"
            );
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
        Log::channel("jobdaily")->info(
            "[Forum]::Starting update. First CID " . $this->_lastUpdatedAccount
        );

        $chunkSize =
            $this->_totalAccounts > 500
                ? ceil($this->_totalAccounts / 12)
                : 100;

        $accountsToUpdate = Account::where(
            "id",
            ">=",
            $this->_lastUpdatedAccount + 1
        )
            ->take($chunkSize)
            ->get();
        foreach ($accountsToUpdate as $acc) {
            if ($acc->setting == null) {
                continue;
            }
            if ($acc->setting->forum_id == null) {
                continue;
            }

            if (
                $acc->is_currently_forum_banned ||
                $acc->data->rating_atc == 0
            ) {
                XenBridge::banForumAccount($acc);
            } else {
                XenBridge::updateForumAccount($acc);
            }

            //hacky for now, do better in V3
            //try {
            //    $gitlab = new Gitlab();
            //    $gitlab->checkNAVAssignments($acc);
            //} catch (Throwable $th) {
            //}
        }
        if ($accountsToUpdate->count() < $chunkSize) {
            Cache::forget("forum.updater.lastUpdatedAccount");
            Log::channel("jobdaily")->info(
                "[Forum]::Finished update. Last CID " .
                    $accountsToUpdate->last()->id
            );
        } else {
            Cache::put(
                "forum.updater.lastUpdatedAccount",
                $accountsToUpdate->last()->id
            );
            Log::channel("jobdaily")->info(
                "[Forum]::Stopped update. Last CID  " .
                    $accountsToUpdate->last()->id
            );
        }
    }
}
