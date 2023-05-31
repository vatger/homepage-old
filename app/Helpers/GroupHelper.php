<?php

namespace App\Helpers;

use App\Models\Membership\Account;
use Exception;
use Junges\ACL\Http\Models\Group;

class GroupHelper
{
    public static function assign(Account $user, $groups = []) {
        try {
            $user->assignGroup($groups);
            return true;
        } catch(Exception $exception) {
            return false;
        }
    }

    public static function assignViaId($user_id, $groups = []) {
        $user = Account::find($user_id);
        if($user == null)
            return false;
        return self::assign($user, $groups);
    }

    public static function revoke(Account $user, $groups = []) {
        try {
            $user->revokeGroup($groups);
            return true;
        } catch(Exception $exception) {
            return false;
        }
    }

    public static function revokeViaId($user_id, $groups = []) {
        $user = Account::find($user_id);
        if($user == null)
            return false;
        return self::revoke($user, $groups);
    }
}
