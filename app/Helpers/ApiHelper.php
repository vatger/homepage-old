<?php

namespace App\Helpers;

use App\Libraries\XenBridge;
use App\Models\Membership\Account;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ApiHelper
{
    public static function updateAccount(Account $account)
    {
        $responseRatings = self::_fetchData(
            $account,
            "/ratings/" . $account->id . "/"
        );
        $responseRatingsTimes = self::_fetchData(
            $account,
            "/ratings/" . $account->id . "/rating_times/"
        );
        if ($responseRatings == false || collect($responseRatings)->isEmpty()) {
            return false;
        }
        if (
            collect(
                $responseRatingsTimes == false || $responseRatingsTimes
            )->isEmpty()
        ) {
            return false;
        }
        $result = self::_insertData(
            $account,
            $responseRatings,
            $responseRatingsTimes
        );
        return $result;
    }

    private static function _insertData(
        Account $account,
        $responseRatings,
        $responseRatingsTimes
    ) {
        try {
            $account->loadMissing("data");
            $account->data->rating_atc = $responseRatings->rating;
            $account->data->registered_at = Carbon::parse(
                $responseRatings->reg_date
            );
            $account->data->time_atc = $responseRatingsTimes->atc;
            $account->data->time_pilot = $responseRatingsTimes->pilot;

            $account->data->save();
            $account->save();
            try {
                if ($account->data->rating_atc == 0) {
                    XenBridge::banForumAccount($account);
                }
            } catch (Exception $exception) {
            }
            return true;
        } catch (Exception $exception) {
            Log::error(" " . $exception->getMessage());
            return false;
        }
    }

    private static function _fetchData(Account $account, $urlEndpoint)
    {
        $uri = config("vatsim_api.base");
        $client = new Client([
            "base_uri" => $uri,
            "headers" => ["Content-type" => "application/json"],
            "connect_timeout" => 25,
        ]);
        try {
            $uri .= $urlEndpoint;
            $response = $client->get($uri);
            if (
                $response->getStatusCode() < 200 ||
                $response->getStatusCode() > 299
            ) {
                return false;
            }
            return json_decode($response->getBody());
        } catch (Exception $exception) {
            return false;
        }
    }
}
