<?php

namespace App\Libraries;

use App\Models\Membership\Account;
use App\Models\TeamSpeak\Registration;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Log;

/**
 * TeamSpeakWebquery
 */
class TeamSpeakWebquery
{
    /**
     * Http Client for all requests
     *
     * @var GuzzleHttp\Client
     */
    protected $_httpClient;

    /**
     * __construct
     *
     * @return void
     */

    //------------------ public static functionS ------------------

    /**
     * registerViaUid
     *
     * @param Account $account
     * @param string $registration_ip
     * @param string $uid
     * @return boolean
     */
    public static function registerViaUid(
        Account $account,
        $registration_ip,
        $uid
    ) {
        $search = self::_clientgetdbidfromuid($uid);
        if ($search == false) {
            return false;
        }
        $clientDBid = $search[0]->cldbid;

        $registration = new Registration();
        $registration->account_id = $account->id;
        $registration->registration_ip = $registration_ip;
        $registration->uid = $uid;
        $registration->dbid = $clientDBid;

        $servergroupId = self::getServergroupId(
            config("teamspeak.default_group")
        );

        $description = $account->username . " (" . $account->id . ")";
        if (self::_clientdbedit($clientDBid, $description) == false) {
            return false;
        }
        if (self::_servergroupaddclient($clientDBid, $servergroupId) == false) {
            return false;
        }
        $registration->save();
        return true;
    }

    /**
     * removeTSRegistation
     *
     * @param Registration $registration
     * @return boolean
     */
    public static function removeTSRegistation(Registration $registration)
    {
        $servergroupId = self::getServergroupId(
            config("teamspeak.default_group")
        );
        $clientDBid = $registration->dbid;

        if (self::_servergroupdelclient($clientDBid, $servergroupId) == false) {
            return false;
        }

        self::_clientdbedit($clientDBid, "");

        $registration->delete();
        return true;
    }

    /**
     * checkClientDB
     *
     * @return void
     */
    /*
    public static function checkClientDB()
    {
        $clients = self::_clientdblist();
        if ($clients == false) {
            return;
        }

        $servergroupId = self::getServergroupId(config('teamspeak.default_group'));
        foreach ($clients as $client) {

        }
    }
    */

    public static function checkClient($client)
    {
        $servergroupId = self::getServergroupId(
            config("teamspeak.default_group")
        );
        $registration = Registration::where(
            "uid",
            $client->client_unique_identifier
        )
            ->where("dbid", $client->cldbid)
            ->first();
        if ($registration == null) {
            // client has no Registration
            self::_servergroupdelclient($client->cldbid, $servergroupId);
            return;
        }
        self::checkRegistration($registration, $client);
    }

    public static function getClientDB($startNr = 0)
    {
        $clients = self::_clientdblist($startNr);
        if ($clients == false) {
            return [];
        }
        return $clients;
    }

    /**
     * checkAccount
     *
     * @param Account $account
     * @return void
     */
    public static function checkAccount(Account $account)
    {
        $account->loadMissing("data");
        $has_active_ban_vatger = $account->getIsCurrentlyTSBannedAttribute();
        $has_active_ban_vatsim = $account->data->rating_atc == 0;

        $has_active_ban = $has_active_ban_vatger || $has_active_ban_vatsim;

        $registrations = Registration::where("account_id", $account->id)->get();
        foreach ($registrations as $registration) {
            $existingTSBans = self::getBansFromRegistration($registration);
            if ($has_active_ban_vatger && empty($existingTSBans)) {
                $ban = $account->currentBan;
                self::_banadd(
                    $registration->uid,
                    Carbon::now()->diffInSeconds($ban->banned_till),
                    "[Account " . $account->id . "]" . $ban->reason
                );
            } else {
                if ($has_active_ban_vatsim && empty($existingTSBans)) {
                    self::_banadd(
                        $registration->uid,
                        0,
                        "[Account " . $account->id . "]" . " Account suspended"
                    );
                }
            }



            if ($has_active_ban == false && !empty($existingTSBans)) {
                foreach ($existingTSBans as $ban) {
                    self::_bandel($ban->banid);
                }
            }
        }
    }

    /**
     * getBansFromRegistration
     *
     * @param Registration $registration
     * @return mixed
     */
    public static function getBansFromRegistration(Registration $registration)
    {
        $allbans = self::_banlist();
        $registrationbans = [];
        if ($allbans == false) {
            return $registrationbans;
        }

        foreach ($allbans as $ban) {
            if (strcmp($ban->uid, $registration->uid) == 0) {
                $registrationbans[] = $ban;
            }
        }
        return $registrationbans;
    }

    /**
     * checkRegistration
     *
     * @param Registration $registration
     * @param stdClass $client
     * @return void
     */
    public static function checkRegistration(
        Registration $registration,
        $client
    ) {
        $registration->last_login = Carbon::createFromTimestamp(
            $client->client_lastconnected
        );
        $registration->last_ip = $client->client_lastip;
        $registration->save();
        $account = $registration->account;
        if ($account == null) {
            return;
        }
        $description = $account->username . " (" . $account->id . ")";
        if (strcmp($client->client_description, $description) != 0) {
            self::_clientdbedit($client->cldbid, $description);
        }
        self::checkAccount($account);
    }

    /**
     * getServergroupId
     *
     * @param string $name
     * @return mixed
     */
    public static function getServergroupId($name)
    {
        return Cache::remember(
            "teamspeak.servergroupid." . $name,
            60,
            function () use ($name) {
                $list = self::_servergrouplist();
                if ($list == false) {
                    return false;
                }
                foreach ($list as $group) {
                    if (
                        strcmp($group->name, $name) == 0 &&
                        strcmp($group->type, 1) == 0
                    ) {
                        return $group->sgid;
                    }
                }
                return false;
            }
        );
    }

    /**
     * _sendWebQuery
     *
     * @param string $command
     * @param array $queryparams
     * @return mixed
     */
    public static function _sendWebQuery($command, $queryparams = [])
    {
        $uri =
            "http://" .
            config("teamspeak.host") .
            ":" .
            config("teamspeak.webquery_port") .
            "/" .
            config("teamspeak.server_number") .
            "/";

        $_httpClient = new Client([
            "base_uri" => $uri,
            "connect_timeout" => 10,
            "read_timeout" => 15,
            "timeout" => 30,
        ]);

        $queryparams["api-key"] = config("teamspeak.apikey");
        $params = [
            "query" => $queryparams,
        ];
        $response = null;
        try {
            $response = $_httpClient->get($command, $params);
        } catch (GuzzleException $e) {
            // code 4xx-5xx
            Log::channel("joberror")->error(
                "[TS] Webquery GuzzleException (Code " .
                    $e->getCode() .
                    "): " .
                    $e->getMessage()
            );
            return false;
        }
        if ($response == null || $response->getStatusCode() != 200) {
            return false;
        }
        $body = json_decode($response->getBody());

        if (empty($body->status) || $body->status->message != "ok") {
            return false;
        }
        if (empty($body->body)) {
            return true;
        }

        return $body->body;
    }

    //------------------ DIRECT API FUNCTIONS ------------------

    /**
     * _servergroupaddclient
     *
     * @param int $clientDBid
     * @param int $servergroupId
     * @return boolean
     */
    private static function _servergroupaddclient($clientDBid, $servergroupId)
    {
        return self::_sendWebQuery("servergroupaddclient", [
            "cldbid" => $clientDBid,
            "sgid" => $servergroupId,
        ]);
    }

    /**
     * _servergroupdelclient
     *
     * @param int $clientDBid
     * @param int $servergroupId
     * @return boolean
     */
    private static function _servergroupdelclient($clientDBid, $servergroupId)
    {
        return self::_sendWebQuery("servergroupdelclient", [
            "cldbid" => $clientDBid,
            "sgid" => $servergroupId,
        ]);
    }

    /**
     * _servergrouplist
     *
     * @return mixed
     */
    private static function _servergrouplist()
    {
        return Cache::remember("teamspeak.servergrouplist", 120, function () {
            return self::_sendWebQuery("servergrouplist");
        });
    }

    /**
     * _clientdbedit
     *
     * @param int $clientDBid
     * @param string $client_description
     * @return void
     */
    private static function _clientdbedit($clientDBid, $client_description = "")
    {
        return self::_sendWebQuery("clientdbedit", [
            "cldbid" => $clientDBid,
            "client_description" => $client_description,
        ]);
    }

    /**
     * _clientdblist
     *
     * @return mixed
     */
    private static function _clientdblist($start = 0)
    {
        return Cache::remember(
            "teamspeak.clientdblist." . $start,
            30,
            function () use ($start) {
                return self::_sendWebQuery("clientdblist", ["start" => $start]);
            }
        );
    }

    /**
     * _clientgetdbidfromuid
     *
     * @param string $uid
     * @return mixed
     */
    private static function _clientgetdbidfromuid($uid)
    {
        return self::_sendWebQuery("clientgetdbidfromuid", ["cluid" => $uid]);
    }

    /**
     * _banlist
     *
     * @return mixed
     */
    private static function _banlist()
    {
        return Cache::remember("teamspeak.banlist", 120, function () {
            return self::_sendWebQuery("banlist");
        });
    }

    /**
     * _banadd
     *
     * @param string $uid
     * @param int $time
     * @param string $text
     * @return boolean
     */
    private static function _banadd($uid, $time, $text = "Banned!")
    {
        Cache::forget("teamspeak.banlist");
        return self::_sendWebQuery("banadd", [
            "uid" => $uid,
            "time" => $time,
            "text" => $text,
        ]);
    }

    /**
     * _bandel
     *
     * @param int $banID
     * @return boolean
     */
    private static function _bandel($banID)
    {
        Cache::forget("teamspeak.banlist");
        return self::_sendWebQuery("bandel", [
            "banid" => $banID,
        ]);
    }

    /**
     * _privilegekeylist
     *
     * @return mixed
     */
    private static function _privilegekeylist()
    {
        return Cache::remember("teamspeak.privilegekeylist", 120, function () {
            return self::_sendWebQuery("privilegekeylist");
        });
    }

    /**
     * _privilegekeyadd
     *
     * @param int $tokentype
     * @param int $groupID
     * @param int $channelID
     * @param string $description
     * @param array $tokencustomset
     * @return mixed
     */
    private static function _privilegekeyadd(
        $tokentype,
        $groupID,
        $channelID,
        $description = "",
        $tokencustomset = []
    ) {
        Cache::forget("teamspeak.privilegekeylist");
        return self::_sendWebQuery(
            "privilegekeyadd",
            ["tokentype" => $tokentype],
            ["tokenid1" => $groupID],
            ["tokenid2" => $channelID],
            ["tokendescription" => $description],
            ["tokencustomset" => $tokencustomset]
        );
    }

    /**
     * _privilegekeydelete
     *
     * @param string $token
     * @return boolean
     */
    private static function _privilegekeydelete($token)
    {
        Cache::forget("teamspeak.privilegekeylist");
        return self::_sendWebQuery("privilegekeydelete", ["token" => $token]);
    }
}
