<?php

namespace App\Console\Commands\TeamSpeak;

use Illuminate\Console\Command;
use App\Libraries\TeamSpeak;
use App\Models\Membership\Account;
use TeamSpeak3_Adapter_ServerQuery_Event;
use TeamSpeak3_Helper_Signal;
use TeamSpeak3_Node_Host;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Transport_Exception;
use TeamSpeak3_Adapter_ServerQuery_Exception;

class TeamSpeakDaemonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ts:daemon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script that should be kept running to ensure TeamSpeak Users are properly registered and not suspended.';

    /**
     * The TeamSpeak Server Connection Interface
     * @var TeamSpeak
     */
    private $_teamSpeak;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        return; //disable cmd
        try{
            $this->_teamSpeak = new TeamSpeak('vACC Germany TeamSpeak Daemon');
        } catch(TeamSpeak3_Transport_Exception $e) {
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return; //disable cmd
        if($this->_teamSpeak->getStatus()) {
            // Register Events
            $this->_teamspeak->getInstance()->notifyRegister('server');
            TeamSpeak3_Helper_Signal::getInstance()->subscribe('notifyCliententerview', self::class.'::clientJoinedEvent');
            TeamSpeak3_Helper_Signal::getInstance()->subscribe('notifyClientleftview', self::class.'::clientLeftEvent');

            while(true) {
                try {

                } catch(TeamSpeak3_Transport_Exception $e) {
                    sleep(30);
                }
            }
        } else {
            sleep(30);

            return $this->handle();
        }
    }

    public static function clientJoinedEvent(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
    {
        if (0 != $event['client_type']) {
            return;
        }
        try {
            $client = $host->serverGetSelected()->clientGetById($event->clid);
            $account = self::checkClient($client);
            self::checkMemberStanding($client, $account);
        } catch (TeamSpeak3_Adapter_ServerQuery_Exception $e) {
            return;
        }
    }

    public static function clientLeftEvent(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
    {
    }

    /**
     * Perform the checks we need to do.
     *
     * @param TeamSpeak3_Node_Client $client [description]
     *
     * @return [type] [description]
     */
    private static function checkClient(TeamSpeak3_Node_Client $client)
    {
        $account = TeamSpeak::checkClient($client);
        if (false !== $account && $account instanceof Account) {
            // Check client description is okay.
            $client = TeamSpeak::clientDescription($client, $account);
        }

        return $account;
    }

    /**
     * If the member has been banned from our systems.. Ban him from TS too
     *
     * @param  TeamSpeak3_Node_Client $client  [description]
     * @param  Account                $account [description]
     * @return [type]                          [description]
     */
    private static function checkMemberStanding(TeamSpeak3_Node_Client $client, Account $account)
    {
        // Check if the account has local / global network bans
        if($account->isCurrentlyBanned) {
            $cb = $account->currentBan;

            TeamSpeak::kickClient($client, $cb->reason);
            sleep(5); // Make sure client is gone prior to the next step!

            $now = \Carbon\Carbon::now();
            TeamSpeak::banClient($client, $cb->reason, $now->diffInSeconds($cb->banned_till));
        }
    }

}
