<?php

namespace App\Http\Middleware\Membership;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class CheckInactiveMiddleware
{

    protected $_authGuard;

    function __construct(Guard $guard)
    {
        $this->_authGuard = $guard;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($this->_authGuard->user()->isInactive) {
            return redirect()->route('membership.inactive');
        }

        return $next($request);
    }
}
