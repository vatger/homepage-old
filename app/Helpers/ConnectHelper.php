<?php

namespace App\Helpers;

use App\Models\Membership\Account;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConnectHelper
{

    public static function updateAccount(Account $account)
    {
        if (Carbon::parse($account->token_expires)->isPast()) {
            $refresh = self::_refreshToken($account);
            if (!$refresh) {
                try {
                    $prevaccount = Auth::user();
                    Auth::setUser($account);
                    Auth::logout();
                    Auth::setUser($prevaccount);
                } catch (\Throwable $th) {
                    //throw $th;
                }
                return false;
                //$account->access_token = null;
                //$account->refresh_token = null;
                //$account->token_expires = null;
                //$account->save();
            }
            $account->access_token = $refresh->access_token;
            $account->refresh_token = $refresh->refresh_token;
            $account->token_expires = now()->addSeconds($refresh->expires_in)->timestamp;
            $account->save();
        }
        $response = self::_fetchAccount($account);
        if ($response == false ||collect($response)->isEmpty()) return false;
        $result = self::_insertData($account, $response);
        return $result;
    }

    private static function _insertData(Account $account, $response)
    {
        try {
            $account->loadMissing('data');
            if (!Account::where('email', $response->data->personal->email)->where('id', '!=', $account->id)->exists()){
                $account->email = $response->data->personal->email; // Need to check first if dupe
            }
            $account->firstname = mb_convert_encoding($response->data->personal->name_first, "Windows-1252", "UTF-8");
            $account->lastname = mb_convert_encoding($response->data->personal->name_last, "Windows-1252", "UTF-8");

            $account->data->rating_atc = $response->data->vatsim->rating->id;
            $account->data->rating_pilot = $response->data->vatsim->pilotrating->id;

            $account->data->active = $account->data->rating_atc > 0;
            $account->data->suspended = $account->data->rating_atc < 0;

            /*
             * Update accounts region/division associations
             */
            // User country
            $account->data->country_code = $response->data->personal->country->id;
            $account->data->country_name = $response->data->personal->country->name;
            // User Region
            $account->data->region_code = $response->data->vatsim->region->id;
            $account->data->region_name = $response->data->vatsim->region->name;
            // User Division
            $account->data->division_code = $response->data->vatsim->division->id;
            $account->data->division_name = $response->data->vatsim->division->name;
            // User Subdivision
            $account->data->subdivision_code = $response->data->vatsim->subdivision->id;
            $account->data->subdivision_name = $response->data->vatsim->subdivision->name;
            $account->data->save();
            $account->save();
            //to many logs
            //activity()
            //   ->causedBy($account)
            //   ->performedOn($account)
            //   ->log("Die SSO Daten wurden automatisch aktualisiert!");
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private static function _fetchAccount(Account $account)
    {
        $uri = config('vatsim_auth.base');
        $client = new Client([
            'base_uri' => $uri,
            'headers' => [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $account->access_token,
            ],
        ]);
        try {
            $response = $client->get($uri . '/api/user');
            return json_decode($response->getBody());
        } catch (\Exception $exception) {
            $account->token_expires = 1;
            $account->save();
            return false;
        }

    }

    private static function _refreshToken(Account $account)
    {
        $uri = config('vatsim_auth.base');
        $client = new Client([
            'base_uri' => $uri,
            'headers' => [
                'Content-type' => 'application/json',
            ],
        ]);
        try {
            $response = $client->post($uri . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $account->refresh_token,
                    'client_id' => config('vatsim_auth.id'),
                    'client_secret' => config('vatsim_auth.secret'),
                    'scope' => str_replace(',', ' ', config('vatsim_auth.scopes')),
                ],
            ]);

            if ($response->getStatusCode() != 200) {
                return false;
            }

            return json_decode($response->getBody());
        } catch (\Exception $exception) {
            return false;
        }

    }
}
