<?php

namespace App\Http\Controllers\Administration\Membership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Junges\ACL\Http\Models\Permission;

class PermissionController extends Controller
{

    function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

            if(!$this->account->hasPermission('administration.membership'))
                abort(403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
    	if($request->ajax()) {
    		return Permission::orderBy('slug', 'ASC')->get();
    	}
    }

}
