<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Auth;
use Carbon\Carbon;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Request::is('api/*')) {
            $locale = 'de';
            if ($request->hasHeader('x-locale')) {
                $locale = $request->header('x-locale');
            }
            app()->setLocale($locale);
            setlocale(LC_TIME, $locale);
            Carbon::setLocale($locale);

            return $next($request);
        }
        if (!Session::has('language') && !Auth::check()) {
            Session::put('language', app()->getLocale());
        }
        if (!Session::has('language') && Auth::check()) {
            Session::put('language', Auth::user()->setting->language);
        }

        app()->setLocale(Session::get('language'));
        setlocale(LC_TIME, Session::get('language'));
        Carbon::setLocale(Session::get('language'));

        $response = $next($request);
        $response->headers->set('x-locale', Session::get('language'));

        return $response;
    }
}
