<?php


namespace App\Models\Events;


use App\Models\Statistic\FlightData;
use Illuminate\Database\Eloquent\Model;
use App\Models\Membership\Account;
use App\Models\Navigation\Aerodrome;

class RouteLeg extends Model
{
    protected $table = "event_routelegs";

    protected $appends = [
        'my_pivot',
    ];

    public function getMyPivotAttribute(){ //hm
        if (!auth()->check()) return null;
        $data = $this->accounts()->wherePivot('account_id', auth()->id())->first();
        if(is_null($data)) return null;
        return $data->pivot;
    }


    public function route(){
        return $this->belongsTo(EventRoute::class, 'route_id', 'id');
    }

    public function accounts() {
        return $this->belongsToMany(Account::class, 'event_account_routelegs','routeleg_id','account_id')->withPivot('completed_at', 'fight_data_id');
    }

    public function departureAerodrome(){
        return $this->hasOne(Aerodrome::class, 'id', 'departureaerodrome_id');
    }

    public function arrivalAerodrome()
    {
        return $this->hasOne(Aerodrome::class, 'id', 'arrivalaerodrome_id');
    }

    public function flightData()
    {
        return $this->hasOne(FlightData::class, 'id', 'flight_data_id');
    }
}
