<?php

namespace App\Libraries;

use App\Models\Forum\ForumGroup;
use App\Models\Membership\Account;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Log;

/**
 * A Library to interact with XenForo 2.1 API
 */
class XenBridge
{
    /**
     * Send an actual call to the XenForo API.
     *
     * @param [type] $endpoint [description]
     * @param [type] $data     [description]
     *
     * @return [type] [description]
     */
    private static function _sendAPICommand($endpoint, $data)
    {
        $data["headers"] = [
            "Accept" => "application/json",
            "XF-Api-Key" => config("forum.apikey"),
            "XP-Api-User" => 1,
        ];
        $data["api_bypass_permissions"] = 1;
        $client = new Client();
        try {
            $response = $client->get(
                config("forum.url") . "/api/" . $endpoint . '?api_bypass_permissions=1' ,
                $data
            );

            return $response;
        } catch (GuzzleException $e) {
            Log::debug($e->getMessage());
            return false;
        }
    }

    /**
     * Send a post request to a XenForo Api.
     *
     * @param [type] $endpoint [description]
     * @param [type] $formData [description]
     *
     * @return [type] [description]
     */
    private static function _sendAPIPostCommand($endpoint, $formData)
    {
        $data["headers"] = [
            "Accept" => "application/json",
            "XF-Api-Key" => config("forum.apikey"),
            "XF-Api-User" => 1,
        ];
        $data["api_bypass_permissions"] = 1;
        $data["form_params"] = $formData;
        $client = new Client();
        try {
            $response = $client->post(
                config("forum.url") . "/api/" . $endpoint,
                $data
            );

            return $response;
        } catch (ClientException $e) {
            Log::debug($e->getMessage());
            return false;
        }
    }

    /**
     * Send an update request to a XenForo API.
     *
     * @param [type] $endpoint [description]
     * @param [type] $formData [description]
     *
     * @return [type] [description]
     */
    private static function _sendAPIPatchCommand($endpoint, $formData)
    {
        $data["headers"] = [
            "Accept" => "application/json",
            "XF-Api-Key" => config("forum.apikey"),
            "XF-Api-User" => 1,
        ];
        $data["api_bypass_permissions"] = 1;
        $data["form_params"] = $formData;
        $client = new Client();
        try {
            $response = $client->put(
                config("forum.url") . "/api/" . $endpoint,
                $data
            );

            return $response;
        } catch (ClientException $e) {
            Log::debug($e->getMessage());
            return false;
        }
    }

    /**
     * Send a delete request to XenForo Api.
     *
     * @param [type] $endpoint [description]
     * @param [type] $formData [description]
     *
     * @return [type] [description]
     */
    private static function _sendAPIDeleteCommand($endpoint, $formData)
    {
        $data["headers"] = [
            "Accept" => "application/json",
            "XF-Api-Key" => config("forum.apikey"),
            "XF-Api-User" => 1,
        ];
        $data["api_bypass_permissions"] = 1;
        $data["form_params"] = $formData;
        $client = new Client();
        try {
            $response = $client->delete(
                config("forum.url") . "/api/" . $endpoint,
                $data
            );

            return $response;
        } catch (ClientException $e) {
            Log::debug($e->getMessage());
            return false;
        }
    }

    /**
     * A function to create an Account for the XenForo Application via API call.
     *
     * @param Account $account [description]
     * @param string $password [description]
     *
     * @return bool
     */
    public static function createForumAccount(
        Account $account,
                $password,
                $try = 0
    )
    {
        if (null != $account->setting->forum_id) {
            return false;
        }
        // Build the data we need from the existing user object
        $dataArray = [];
        // Set all options we need as defaults
        $dataArray["option"] = [
            "content_show_signature" => true,
            "email_on_conversation" => true,
            "push_on_conversation" => true,
            "receive_admin_email" => true,
            "show_dob_year" => false,
            "show_dob_date" => false,
            "is_discouraged" => false,
        ];
        // Profile specific Account-Data
        $dataArray["profile"] = [
            "location" => "",
            "website" => "",
            "about" => "",
            "signature" => "",
        ];
        $dataArray["visible"] = true;
        $dataArray["activity_visible"] = true;
        $dataArray["timezome"] = "Europe/Berlin";
        $dataArray["custom_title"] = $account->id;

        // Is nothing else than $account->firstname.' '.$account->lastname
        if (0 == $try) {
            $dataArray["username"] = $account->username;
        } else {
            $dataArray["username"] = $account->username . " " . $try;
        }
        $dataArray["email"] = $account->email;
        // Set Default Usergroup
        $dataArray["user_group_id"] = config("forum.defaultGroup"); // Default Registered User Group
        $dataArray["is_staff"] = false;

        $dataArray["password"] = $password;

        $dataArray["custom_fields"] = [
            "vatsimid" => $account->id,
        ];

        $result = self::_sendAPIPostCommand("users", $dataArray);
        if ($result && 200 == $result->getStatusCode()) {
            $body = $result->getBody()->getContents();
            $forumUserObject = json_decode($body);
            $accSetting = $account->setting;
            $accSetting->forum_id = $forumUserObject->user->user_id;
            $accSetting->save();

            return true;
        } else {
            ++$try;
            if ($try <= 99) {
                return self::createForumAccount($account, $password, $try);
            }
        }

        return false;
    }

    /**
     * Update an account on the forum.
     *
     * @param Account $account [description]
     *
     * @return [type] [description]
     */
    public static function updateForumAccount(Account $account)
    {
        $account->loadMissing("data", "groups", "regionalgroups");

        if (null == $account->setting) {
            return false;
        }

        if (null == $account->setting->forum_id) {
            return false;
        }

        if (
            $account->data->rating_atc == 0 ||
            $account->is_currently_forum_banned
        ) {
            self::banForumAccount($account);
        }

        $dataArray = [];
        /**
         * Issue 133.
         * Update username when cert data was updated.
         *
         * 1. Get current user object from the board
         * 2. Compare the names
         * 3. If mismatch: set new name
         */
        $result = self::_sendAPICommand(
            "users/" . $account->setting->forum_id,
            []
        );

        if (!$result) {
            return false;
        }

        $response = json_decode($result->getBody()->getContents());
        $forumUserObject = $response->user;

        if (
            $forumUserObject &&
            trim(preg_replace("/[0-9]+/", "", $forumUserObject->username)) !=
            $account->username
        ) {
            $dataArray["username"] = $account->username;
        }

        /**
         * Array to store forum groups that must be assigned to the user.
         * @var array
         */
        $secondaryGroups = [];

        // Get all forumgroups the user has through assigned groups
        foreach ($account->groups as $grp) {
            $grp->loadMissing("forumgroups");
            foreach ($grp->forumgroups as $fg) {
                if (!array_key_exists($fg->forum_id, $secondaryGroups)) {
                    $secondaryGroups[] = $fg->forum_id;
                }
            }
        }

        /**
         * Assign forum groups based upon regionalgroup status
         *
         */
        $fgrps = ForumGroup::all();
        foreach ($account->regionalgroups as $rg) {
            if (
                $rg->chief_id == $account->id ||
                $rg->deputy_id == $account->id
            ) {
                // Account is staff team
                if ($fgrps->contains($rg->staff_group_id)) {
                    $secondaryGroups[] = $fgrps->firstWhere(
                        "id",
                        $rg->staff_group_id
                    )->forum_id;
                }
            }

            if ($account->isMentorOfRegionalgroup($rg)) {
                // Mentoring group
                if ($fgrps->contains($rg->mentor_group_id)) {
                    $secondaryGroups[] = $fgrps->firstWhere(
                        "id",
                        $rg->mentor_group_id
                    )->forum_id;
                }
            }

            if ($account->isNavigatorOfRegionalgroup($rg)) {
                // Nav group
                if ($fgrps->contains($rg->navler_group_id)) {
                    $secondaryGroups[] = $fgrps->firstWhere(
                        "id",
                        $rg->navler_group_id
                    )->forum_id;
                }
            }

            if ($account->isEventlerOfRegionalgroup($rg)) {
                // Eventteam group
                if ($fgrps->contains($rg->eventler_group_id)) {
                    $secondaryGroups[] = $fgrps->firstWhere(
                        "id",
                        $rg->eventler_group_id
                    )->forum_id;
                }
            }

            if ($account->isMemberOfRegionalgroup($rg)) {
                // Fullmemeber, give acces to membership group
                if ($fgrps->contains($rg->member_group_id)) {
                    $secondaryGroups[] = $fgrps->firstWhere(
                        "id",
                        $rg->member_group_id
                    )->forum_id;
                }
                // Fullmember, give access to the voting group
                // But only if at least 2 month non stop membership
                if (
                    $rg->pivot->created_at <= Carbon::now()->subMonth(2) &&
                    $fgrps->contains($rg->voting_group_id)
                ) {
                    $secondaryGroups[] = $fgrps->firstWhere(
                        "id",
                        $rg->voting_group_id
                    )->forum_id;
                }
                // If you are assigend to a regionalgroup you are eligable to vote as a vacc member
                // But only if you are a member for at least 2 month
                if ($rg->pivot->created_at <= Carbon::now()->subMonth(2)) {
                    $secondaryGroups[] = 44;
                } // vACC-Forum voting group id => 44;
            }

            if ($account->isGuestOfRegionalgroup($rg)) {
                if ($fgrps->contains($rg->guest_group_id)) {
                    $secondaryGroups[] = $fgrps->firstWhere(
                        "id",
                        $rg->guest_group_id
                    )->forum_id;
                }
            }
        }

        // OBSOLETE: NOT THE RIGHT WAY OF DOING THIS
        // Determine if the user is allowed to participate in vACC wide votings.
        // vACC-Forum voting group id => 44;
        // if ($account->created_at <= \Carbon\Carbon::now()->subMonth(2)) {
        //     if ('GER' == $account->data->subdivision_code) {
        //         // Definitly member
        //         $secondaryGroups[] = 44;
        //     } elseif ('EUD' == $account->data->division_code && 'EUR' == $account->region_code && null == $account->data->subdivision_code) {
        //         // They might be pilots that do not have set their subdivision
        //         // TODO: find a better way of checking this. Maybe force any real vACC member to set their subdivision to GER
        //         // The current way does also include non-vACC members that are within the european region.
        //         $secondaryGroups[] = 44;
        //     }
        // }

        $dataArray["secondary_group_ids"] = [];
        if (!empty($secondaryGroups)) {
            $dataArray["secondary_group_ids"] = $secondaryGroups;
        } else {
            $dataArray["secondary_group_ids"][] = config("forum.guestGroup");
        }

        $result = self::_sendAPIPostCommand(
            "users/" . $account->setting->forum_id,
            $dataArray
        );
        if (!$result) {
            return false;
        }
        $response = json_decode($result->getBody()->getContents());
        if ($response->success) {
            // $account->notify(new \App\Notifications\Forum\AccountUpdatedNotification($secondaryGroups));

            // activity()
            //    ->causedBy($account)
            //    ->performedOn($account)
            //    ->log("Der Forenaccount wurde entsprechend der neusten Daten aktualisiert!");

            return true;
        }

        return false;
    }

    /**
     * Sets a user discouraged and assigns the suspended group.
     *
     * @param Account $account [description]
     * @return [type]           [description]
     */
    public static function banForumAccount(Account $account)
    {
        if (null == $account->setting) {
            return false;
        }

        if (null == $account->setting->forum_id) {
            return false;
        }


        $suspendedGroup = config("forum.suspendedGroup");

        // Build the data we need from the existing user object
        $dataArray = [];
        // Set all options we need as defaults
        $dataArray["option"] = [
            "is_discouraged" => false,
        ];
        $dataArray["secondary_group_ids"] = [];
        $dataArray["secondary_group_ids"][] = $suspendedGroup;

        $result = self::_sendAPIPostCommand(
            "users/" . $account->setting->forum_id,
            $dataArray
        );
        if (!$result) {
            return false;
        }
        $response = json_decode($result->getBody()->getContents());
        if ($response->success) {
            // $account->notify(new \App\Notifications\Forum\AccountUpdatedNotification($secondaryGroups));

            // activity()
            //    ->causedBy($account)
            //    ->performedOn($account)
            //    ->log("Der Forenaccount wurde entsprechend der neusten Daten gesperrt!");

            return true;
        }

        return false;
    }

    /**
     * Sends a private message to a user in the forum
     * @param  [type] $account [description]
     * @param  [type] $title   [description]
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public static function sendAccountNotification(Account $account, $title, $message)
    {
        if (null == $account->setting->forum_id) {
            return false;
        }

        $dataArray["recipient_ids"] = [$account->setting->forum_id];

        $dataArray["title"] = $title;
        $dataArray["message"] = $message;
        $dataArray["open_invite"] = false;

        $result = self::_sendAPIPostCommand("conversations", $dataArray);
        if ($result && 200 == $result->getStatusCode()) {
            return true;
        }
        return false;
    }

    /**
     * Delete an forum account.
     *
     * @param [type] $forumId [description]
     * @param [type] $vid     [description]
     *
     * @return [type] [description]
     */
    public static function deleteForumAccount($forumId, $vid)
    {
        $result = self::_sendAPIDeleteCommand("users/" . $forumId, [
            "rename_to" => $vid,
        ]);
        if ($result && 200 == $result->getStatusCode()) {
            return true;
        }

        return false;
    }

    /**
     * Get the actual username used for this account on the forums
     *
     * @param Account $account
     *
     * @return String The actual username
     */
    public static function getForumUsername(Account $account)
    {
        if(!$account->setting->forum_id) return false;
        $result = self::_sendAPICommand(
            "users/" . $account->setting->forum_id,
            []
        );
        if (!$result) {
            return false;
        }

        $response = json_decode($result->getBody()->getContents());
        $forumUserObject = $response->user;
        return $forumUserObject->username;
    }


    public static function getForumEmail(Account $account)
    {
        if(!$account->setting->forum_id) return false;
        $result = self::_sendAPICommand(
            "users/" . $account->setting->forum_id,
            []
        );
        if (!$result) {
            return false;
        }

        $response = json_decode($result->getBody()->getContents());
        $forumUserObject = $response->user;
        return $forumUserObject->email;
    }

    /**
     * Grab the threads from the news forum
     * @return [type] [description]
     */
    public static function getNewsThreads()
    {
        $result = self::_sendAPICommand(
            "forums/" . config("forum.newsId") . "/threads",
            []
        );
        if ($result && 200 == $result->getStatusCode()) {
            return json_decode($result->getBody()->getContents());
        }

        return false;
    }

    /**
     * Grab the posts in the news threads
     * @param  [type] $postId [description]
     * @return [type]           [description]
     */
    public static function getPost($postId)
    {
        $result = self::_sendAPICommand("posts/" . $postId, []);
        if ($result && 200 == $result->getStatusCode()) {
            return json_decode($result->getBody()->getContents());
        }

        return false;
    }
}
