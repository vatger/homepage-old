<?php

namespace App\Libraries;

use TeamSpeak3;
use TeamSpeak3_Exception;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Adapter_ServerQuery_Exception;
use App\Models\Membership\Account;
use App\Models\TeamSpeak\Registration;

/**
 * TeamSpeak 3 Server Query Interface
 */
class TeamSpeak
{

	/**
	 * The Server Query Connection Instance
	 * @var [type]
	 */
	private $_instance;

	function __construct($nickname, $blocking = false)
	{

		$tsUrl = sprintf(
            'serverquery://%s:%s@%s:%s/?nickname=%s&server_port=%s%s&use_offline_as_virtual=1#no_query_clients',
            urlencode(config('teamspeak.user')),
            urlencode(config('teamspeak.pass')),
            config('teamspeak.host'),
            config('teamspeak.query_port'),
            urlencode($nickname),
            config('teamspeak.port'),
            $blocking ? '&blocking=0' : ''
        );

        $this->_instance = TeamSpeak3::factory($tsUrl);

	}

	/**
	 * Gets an instance of the serverquery connection
	 *
	 * @return [type] [description]
	 */
	public function getInstance()
	{
		return $this->_instance;
	}

	/**
	 * Is a given virtual server available?
	 *
	 * @return [type] [description]
	 */
	public function getStatus()
	{
		return $this->_instance->getProperty('virtualserver_status') == 'online';
	}

	/**
	 * Does a client has been registered properly?
	 *
	 * @param  TeamSpeak3_Node_Client $client  [description]
	 * @param  integer                $attempt [description]
	 * @return [type]                          [description]
	 */
	public static function checkClient(TeamSpeak3_Node_Client $client, $attempt = 0)
	{
		$registration = self::getActiveRegistration($client);
		if(!is_null($registration)) {
			self::updateClientInfo($client, $registration);

			return $registration->account;
		}
		$registration = self::getNewRegistration($client);
		if(is_null($registration)) {
			if($attempt > 3) {
				self::kickClient($client, 'Bitte auf der Website registrieren!');
				return false;
			} else {
				self::pokeClient($client, 'Bitte auf der Website registrieren!');
				self::messageClient($client, 'Keine gültige Registrierung gefunden. Bitte auf der Homepage registrieren. Warnung '.($attempt + 1).'/3');
                // awaiting manual ID input from Membership Settings
                sleep(30);

                return self::checkRegistration($client, ++$attempt);
			}
		}
		self::completeNewRegistration($client, $registration);
		return $registration->account;
	}

	/**
     * Grab the current registration from the database.
     *
     * @param TeamSpeak3_Node_Client $client The current client node
     *
     * @return Registration
     */
    protected static function getActiveRegistration(TeamSpeak3_Node_Client $client)
    {
        return Registration::where('uid', $client['client_unique_identifier'])
            ->where('dbid', $client['client_database_id'])
            ->first();
    }

    /**
     * Try to find a not completed registration process
     * Then find out if it matches to the client.
     *
     * @param TeamSpeak3_Node_Client $client [description]
     *
     * @return mixed|Registration|null [description]
     */
    protected static function getNewRegistration(TeamSpeak3_Node_Client $client)
    {
        // Try some magic
        try {
            $customInfo = $client->customInfo();
        } catch (TeamSpeak3_Adapter_ServerQuery_Exception $e) {
            if (1281 !== $e->getCode()) { // Database Empty Result Set
                throw $e;
            } else {
                return;
            }
        }
        // Find the set registration_id in the custom info
        // and if found grab the registration
        foreach ($customInfo as $info) {
            if ('registration_id' == $info['ident']) {
                $reg = Registration::where('id', $info['value'])->whereNull('dbid')->first();

                return $reg;
            }
        }
    }

    /**
     * Update an incomplete registration.
     *
     * @param TeamSpeak3_Node_Client $client       [description]
     * @param Registration           $registration [description]
     */
    protected static function completeNewRegistration(TeamSpeak3_Node_Client $client, Registration $registration)
    {
        if ($registration->confirmation) {
            $registration->confirmation->delete();
        }
        $registration->uid = $client['client_unique_identifier'];
        $registration->dbid = $client['client_database_id'];
        $registration->save();

        event(new \App\Events\TeamSpeak\RegistrationCompletedEvent($registration->account, true));

        self::updateClientInfo($client, $registration);
    }

    /**
     * Update client information.
     *
     * @param TeamSpeak3_Node_Client $client       [description]
     * @param Registration           $registration [description]
     */
    protected static function updateClientInfo(TeamSpeak3_Node_Client $client, Registration $registration)
    {
        $registration->last_login = \Carbon\Carbon::now();
        $registration->last_ip = $client['connection_client_ip'];
        $registration->last_os = $client['client_platform'];
        $registration->save();
    }

    /**
     * Does the description of a client match the attributes from cert?
     *
     * @param TeamSpeak3_Node_Client $client  [description]
     * @param Account                $account [description]
     *
     * @return TeamSpeak3_Node_Client
     */
    public static function clientDescription(TeamSpeak3_Node_Client $client, Account $account)
    {
        $description = $account->username.' ('.$account->id.')';
        if ($client->infoDb()['client_description'] !== $description) {
            $client->modify(['client_description' => $description]);
            $client->getParent()->clientListReset();
            $client = $client->getParent()->clientGetById($client->getId());
        }

        return $client;
    }

	/**
     * Send a private text message to the client.
     *
     * @param TeamSpeak3_Node_Client $client  [description]
     * @param string                 $message [description]
     *
     * @return [type] [description]
     */
    public static function messageClient(TeamSpeak3_Node_Client $client, $message)
    {
        $client->message($message);
    }

    /**
     * Send a poke message to a client.
     *
     * @param TeamSpeak3_Node_Client $client  [description]
     * @param string                 $message [description]
     *
     * @return [type] [description]
     */
    public static function pokeClient(TeamSpeak3_Node_Client $client, $message)
    {
        $client->poke($message);
    }

    /**
     * Kicks a client from the server.
     *
     * @param TeamSpeak3_Node_Client $client  [description]
     * @param string                 $message [description]
     *
     * @return [type] [description]
     */
    public static function kickClient(TeamSpeak3_Node_Client $client, $message)
    {
        $client->kick(TeamSpeak3::KICK_SERVER, $message);
    }

    /**
     * Bans the client.
     *
     * @param TeamSpeak3_Node_Client $client
     * @param string $reason
     * @param int $duration Duration in seconds.
     */
    public static function banClient(TeamSpeak3_Node_Client $client, $reason, $duration)
    {
        self::pokeClient($client, $reason);
        $client->ban($duration, $reason);
    }

}
