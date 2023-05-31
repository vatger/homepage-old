<?php

namespace App\Http\Controllers\Administration\Services;

use App\Libraries\Gitlab;

class GitlabController extends \App\Http\Controllers\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {

            if (!$this->account->hasPermission('administration.services.gitlab')) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function createAccount()
    {
        //$gitlab = new Gitlab();
        //$status = $gitlab->createAccount($this->account);
        //$gitlab->checkNAVAssignments($this->account);
        //if($status == true) return;
        //else
        abort(499);
    }

    public function getSettings()
    {
        return \Response::json($this->account->setting);
    }

}
