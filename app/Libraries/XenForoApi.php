<?php

namespace App\Libraries;

use App\Models\Membership\Account;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class XenForoApi
{
    const TIMEOUT = 30;

    static function add_user_badge(Account $user, int $badge_id) : bool
    {
        if(!$user->setting->forum_id) return false;
        $user_forum_id = $user->setting->forum_id;
        $res = self::send_request('POST', 'badge/add', [
            'user_id' => $user_forum_id,
            'badge_id' => $badge_id,
        ]);
        return $res != false;
    }


    protected static function send_request(string $method, string $endpoint, $data = [])
    {
        $baseUrl = config('forum.extraurl');
        $accessToken = config("forum.extratoken");
        $client = new Client([
            'base_uri' => $baseUrl,
            'connect_timeout' => self::TIMEOUT,
            'read_timeout' => self::TIMEOUT,
            'timeout' => self::TIMEOUT,
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ]
        ]);
        $res = false;
        try {
            $res = $client->request($method, $endpoint, [
                'json' => $data,
            ]);
        } catch (GuzzleException $e) {
            return false;
        }
        return json_decode($res->getBody()->getContents(), true);
    }
}
