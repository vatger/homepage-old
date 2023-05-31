<?php


namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\Events\EventRoute;
use App\Models\Events\RouteLeg;
use Illuminate\Http\Request;

class EventRouteController extends Controller
{
    public function __construct() {
        parent::__construct();
    }

    public function getEventRoutes(Request $request){
        //if(!$request->ajax()) abort(403);
        return EventRoute::withCount('legs')->get();
    }

    public function getRouteLegs(Request $request, EventRoute $route){
        if(!$request->ajax()) abort(403);
        $legs = $route->legs()->with('departureAerodrome','arrivalAerodrome')->get();
        return $legs;
    }

    public function signupEventRoute(Request $request, EventRoute $route){
        if(!$request->ajax()) abort(403);
        foreach ($route->legs as $leg){
           $leg->accounts()->detach($this->account);
           $leg->accounts()->attach($this->account);
        }
    }

    public function signoutEventRoute(Request $request, EventRoute $route){
        if(!$request->ajax()) abort(403);
        foreach ($route->legs as $leg){
            $leg->accounts()->detach($this->account);
        }
    }

}
