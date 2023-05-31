<?php

namespace App\Helpers;

use App\Models\Membership\Account;
use Exception;
use Junges\ACL\Http\Models\Permission;

class PermissionHelper
{
    public function assign(Account $user, $permissions = []) {
        try {
            $user->assignPermissions($permissions);
            return true;
        } catch(Exception $exception) {
            return false;
        }
    }

    public function revoke(Account $user, $permissions = []) {
        try {
            $user->revokePermissions($permissions);
            return true;
        } catch(Exception $exception) {
            return false;
        }
    }
}