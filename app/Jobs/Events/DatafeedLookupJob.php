<?php

namespace App\Jobs\Events;

use App\Libraries\XenBridge;
use App\Libraries\XenForoApi;
use App\Models\Membership\Account;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Statistic\FlightData;
use App\Models\Events\EventRoute;
use App\Models\Events\RouteLeg;
use Illuminate\Support\Facades\Log;

class DatafeedLookupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $timestamp;

    public EventRoute $eventroute;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60*5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(EventRoute $eventroute)
    {
        $this->timestamp = \Carbon\Carbon::now()->utc();
        $this->eventroute = $eventroute;
        $this->eventroute->loadMissing('legs.arrivalAerodrome', 'legs.departureAerodrome', 'legs.accounts');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void
    {
        Log::channel('jobdaily')->info('[Events]::DatafeedLookupJob: Working on ' . $this->eventroute->name);
        $this->handleLegs();
        $this->handleBadges();
        Log::channel('jobdaily')->info('[Events]::DatafeedLookupJob: Working on ' . $this->eventroute->name . ' finished');
    }

    private function handleBadges() : void
    {
        if ($this->eventroute->legs()->count() == 0) return;
        if (is_null($this->eventroute->forum_badge_id)) return;

        $badge_account_ids = $this->eventroute->legs[0]->accounts
            ->map(function ($acc){ return $acc->id; });

        foreach($this->eventroute->legs as $leg) {
            $accounts = $leg->accounts()->wherePivotNotNull('completed_at')->get()
                ->map(function ($acc){ return $acc->id; });;
            $badge_account_ids = $badge_account_ids->intersect($accounts);
        }

        foreach ($badge_account_ids as $badge_account_id)
        {
            $account = Account::query()->where('id', $badge_account_id)->first();
            $badge_added = XenForoApi::add_user_badge($account, $this->eventroute->forum_badge_id);
            if($badge_added) {
                // Send Forum notification
                $title = "Tour " . $this->eventroute->name . " abgeschlossen";
                $message = " Hallo " . $account->firstname .", \n
                herzlichen Glückwunsch! Du hast die Tour " . $this->eventroute->name . " erfolgreich abgeschlossen und erhältst deshalb das Badge als Anerkennung für deine Leistung.
                Wir hoffen, dass du die Tour genossen und dabei neue Erfahrungen gesammelt hast. Das Badge ist eine besondere Auszeichnung für dich, um deine Leistung zu feiern und zu zeigen,
                dass du ein wahrer Abenteurer bist.
                Wir freuen uns darauf, dich bei zukünftigen Touren wieder begrüßen zu dürfen.\n
                Beste Grüße,\n
                das VATGER Touren Team";

                XenBridge::sendAccountNotification($account, $title, $message);
            }
        }

    }


    private function handleLegs() : void
    {
        foreach($this->eventroute->legs as $leg) {
            $this->handleLeg($leg);
        }
    }

    private function handleLeg(RouteLeg $leg) : void
    {
        $pilots = FlightData::completed()
            ->where('departed_at', '>=', $this->eventroute->begins_at)
            ->where('arrived_at', '<=', $this->eventroute->ends_at)
            ->where('departure_airport', 'like', $leg->departureAerodrome->icao)
            ->where('arrival_airport', 'like', $leg->arrivalAerodrome->icao)
            ->whereIntegerInRaw('account_id', $this->getLegParticipants($leg))
            ->get();

        foreach ($pilots as $pilot) {
            $this->_checkCompletedLeg($pilot, $leg);
        }
    }


    private function getLegParticipants(RouteLeg $leg): array
    {
        $participating_ids = [];
        foreach ($leg->accounts as $acc)
            $participating_ids[] = $acc->id;
        return $participating_ids;
    }



    private function _checkCompletedLeg(FlightData $flightData, RouteLeg $leg){
        $eventRoute = $this->eventroute;
        $account = $leg->accounts()->wherePivot('account_id', $flightData->account_id)->first();
        if(is_null($account) || is_null($account->pivot)) return; //should not happen
        $pivot = $account->pivot;

        if($flightData->arrived_at < $pivot->completed_at) return;
        if($this->eventroute->flight_rules == 'V' && $flightData->flight_type != 'V') return;
        if($this->eventroute->flight_rules == 'I' && $flightData->flight_type != 'I') return;
        if(!empty($this->eventroute->aircrafts) && !in_array($flightData->aircraft, explode(",", $this->eventroute->aircrafts))) return; //we use the aircraft_short in vatsim json so no equipment

        if(!$this->eventroute->require_order) {
            $leg->accounts()->updateExistingPivot($flightData->account_id, ['completed_at' => $flightData->arrived_at, 'fight_data_id' => $flightData->id]);
        } else {
            $last_leg_completed = \Carbon\Carbon::parse($this->eventroute->begins_at);
            foreach ($this->eventroute->legs as $otherleg) {
                $otherlegaccount = $otherleg->accounts()->wherePivot('account_id', $flightData->account_id)->first();
                if(is_null($otherlegaccount) || is_null($otherlegaccount->pivot)) return; //should not happen
                if ($leg->id == $otherleg->id && \Carbon\Carbon::parse($flightData->arrived_at)->isAfter($last_leg_completed)){
                    //the leg we want to check is after the previous leg
                    $leg->accounts()->updateExistingPivot($flightData->account_id, ['completed_at' => $flightData->arrived_at, 'fight_data_id' => $flightData->id]);
                    return;
                } elseif (is_null($otherlegaccount->pivot->completed_at)){
                    //one of the previous legs is not completed
                    return;
                } else {
                    //until now all legs have been completed
                    $last_leg_completed = \Carbon\Carbon::parse($otherlegaccount->pivot->completed_at);
                }
            }
        }

    }

}
