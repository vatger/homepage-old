<?php

namespace App\Http\Middleware\Membership;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class CheckBannedMiddleware
{

    protected $_authGuard;

    function __construct(Guard $guard)
    {
        $this->_authGuard = $guard;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($this->_authGuard->user()->is_currently_homepage_banned) {
            return redirect()->route('membership.banned');
        }

        return $next($request);
    }
}
