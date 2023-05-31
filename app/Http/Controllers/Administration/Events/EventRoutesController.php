<?php


namespace App\Http\Controllers\Administration\Events;

use \App\Http\Controllers\Controller;
use App\Models\Events\EventRoute;
use App\Models\Events\RouteLeg;
use App\Models\Membership\Account;
use App\Models\Navigation\Aerodrome;
use App\Models\Statistic\FlightData;
use Illuminate\Http\Request;


class EventRoutesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            if(!$request->ajax()) abort(400);
            if(!$this->account->hasPermission('administration.event.routes')) abort(403);
            return $next($request);
        });
    }

    public function listRoutes()
    {
        return EventRoute::all();
    }

    public function routeDetails(EventRoute $er)
    {
        return $er->legs()->with('accounts:id,firstname,lastname')->with('departureAerodrome')->with('arrivalAerodrome')->get();
    }


    public function createRoute(Request $request){
        $validated = $request->validate([
            'route.name' => 'required|string',
            'route.begins_at' => 'required|date',
            'route.ends_at' => 'required|date',
            'route.require_order' => 'required|integer',
            'route.flight_rules' => 'nullable|string',
            //'route.aircrafts' => 'string',
        ]);
        $er = new EventRoute();
        $er->name = $validated['route']['name'];
        $er->description = "";
        $er->link = "";
        $er->img_url = "";
        $er->begins_at = $validated['route']['begins_at'];
        $er->ends_at = $validated['route']['ends_at'];
        $er->require_order = $validated['route']['require_order'];
        $er->flight_rules = $validated['route']['flight_rules'];
        $er->aircrafts = '';

        $er->save();
        return $er;
    }

    public function editRoute(EventRoute $er, Request $request){

        //todo do all fields

        $validated = $request->validate([
            'route.name' => 'required|string',
            'route.description' => 'string|nullable',
            'route.link' => 'string|nullable',
            'route.img_url' => 'string|nullable',
            'route.begins_at' => 'required|date',
            'route.ends_at' => 'required|date',
            'route.require_order' => 'required|integer',
            'route.flight_rules' => 'nullable|string',
            'route.aircrafts' => 'string|nullable',
            'route.forum_badge_id' => 'integer|nullable',
        ]);
        $er->name = $validated['route']['name'];
        $er->description = empty($validated['route']['description']) ? '' : $validated['route']['description'];
        $er->link = empty($validated['route']['link']) ? '' : $validated['route']['link'];
        $er->img_url = empty($validated['route']['img_url']) ? '' : $validated['route']['img_url'];
        $er->begins_at = $validated['route']['begins_at'];
        $er->ends_at = $validated['route']['ends_at'];
        $er->require_order = $validated['route']['require_order'];
        $er->flight_rules = $validated['route']['flight_rules'];
        $er->aircrafts = empty($validated['route']['aircrafts']) ? '' : $validated['route']['aircrafts'];
        $er->forum_badge_id = empty($validated['route']['forum_badge_id']) ? NULL : $validated['route']['forum_badge_id'];

        $er->save();
        return $er;
    }

    public function deleteRoute(EventRoute $er){
        foreach ($er->legs as $leg) $this->deleteLeg($er, $leg);
        $er->delete();
    }

    public function createLeg(EventRoute $er, Request $request){
        $validated = $request->validate([
            'leg.arrivalaerodrome_icao' => 'required|exists:App\Models\Navigation\Aerodrome,icao',
            'leg.departureaerodrome_icao' => 'required|exists:App\Models\Navigation\Aerodrome,icao',
        ]);
        $leg = new RouteLeg();
        $leg->route_id = $er->id;
        $leg->arrivalaerodrome_id =  Aerodrome::where('icao', 'LIKE', $validated['leg']['arrivalaerodrome_icao'])->firstOrFail()->id;
        $leg->departureaerodrome_id =  Aerodrome::where('icao', 'LIKE', $validated['leg']['departureaerodrome_icao'])->firstOrFail()->id;
        $leg->save();

        // attach the already participating accounts
        if($er->legs()->count() > 0){
            foreach ($er->legs()->first()->accounts  as $acc)
                $leg->accounts()->attach($acc);
        }
        return $leg;
    }

    public function deleteLeg(EventRoute $er, RouteLeg $leg){
        $leg->accounts()->detach();
        return $leg->delete();
    }

    public function manualCompleteLeg(EventRoute $er, RouteLeg $leg, Request $request){
        $validated = $request->validate([
            'account_id' => 'required|exists:App\Models\Membership\Account,id',
            'completed_at' => 'required|date',
        ]);

        $leg->accounts()->updateExistingPivot($validated['account_id'],['completed_at' => $validated['completed_at'], 'fight_data_id' => null]);
    }

    public function showFlightData(FlightData $flightdata){
        return $flightdata;
    }
}
