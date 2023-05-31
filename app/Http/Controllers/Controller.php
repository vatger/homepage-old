<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\Membership\Account;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * The active account.
     *
     * @var \App\Models\Membership\Account | null
     */
    protected $account;

    public function __construct()
    {
        $this->middleware(
            function ($request, $next) {
                // Check if we have a user signed in
                $this->account = new Account(); //Make sure there always is a "User"-Object set
                if (Auth::check() || Auth::guard('web')->check()) {
                    $this->account = Auth::user();
                }

                return $next($request);
            }
        );
    }

    /**
     * Craft application views.
     *
     * @param [type] $view [description]
     *
     * @return [type] [description]
     */
    protected function viewMake($view)
    {
        $view = View::make($view);
        $view->with('_account', $this->account);

        return $view;
    }
}
