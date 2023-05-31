<?php

namespace App\Http\Controllers;

use App\Models\Membership\Account;
use App\Models\TeamSpeak\Registration;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class APIController extends Controller
{
    public function dbids(string $account_id, Request $request)
    {
        $header = $request->header('Authorization', '');
        $token = str_replace('Token ', '', $header);
        if(strcmp($token, config('teamspeak.homepage_api_key')) != 0)
        {
            abort(400,'API incorrect or not provided');
            return;
        }
        $regs = Registration::query()->where('account_id',$account_id)->get();
        $res = [];
        foreach ($regs as $r)
        {
            $res[] = $r->dbid;
        }
        return response()->json($res);
    }

    public function isger(string $account_id, Request $request)
    {
        $acc = Account::query()->where('id', $account_id)->first();
        if(empty($acc)) return  response()->json(false);
        $acc->loadMissing('regionalgroups');
        return  response()->json($acc->regionalgroups->count() > 0);
    }
}
