<?php

namespace App\Http\Controllers\Regionalgroup;

use App\Helpers\GroupHelper;
use App\Http\Controllers\Controller;
use App\Models\Regionalgroups\Regionalgroup;
use Illuminate\Http\Request;
use App\Models\Regionalgroups\RegionalgroupRequest;
use App\Notifications\Administration\Regionalgroups\RequestAcceptedNotification;
use App\Notifications\Regionalgroups\RegionalgroupRequestExistsNotification;

class RequestController extends Controller
{
    public function index(Request $request)
    {
		if($request->ajax()) {
            $requests = $this->account->regionalgroupRequests()->with('regionalgroup:id,name')->get();
            foreach ($requests as $req ) {
                $req->regionalgroup->setAppends([]);
            }
            return $requests;
		}
		abort(403);
	}

	public function create(Request $request)
	{
        abort(422, 'disabled');
    }

    private function _autoAcceptChangeRequest(RegionalgroupRequest $regionalgroupRequest){
        $regionalgroup =  $regionalgroupRequest->regionalgroup;
        if ($regionalgroup->chief_id == $regionalgroupRequest->account->id || $regionalgroup->deputy_id == $regionalgroupRequest->account->id) {
            return; //we better not do this for rg chief/deputy
        }

        GroupHelper::revoke($regionalgroupRequest->account, ['rg.nav', 'rg.event', 'rg.mentor']);
        $regionalgroup->mentors()->detach($regionalgroupRequest->account);
        $regionalgroup->eventler()->detach($regionalgroupRequest->account);
        $regionalgroup->navigators()->detach($regionalgroupRequest->account);

        // Shortly remove from RG
        $regionalgroup->accounts()->detach($regionalgroupRequest->account);

        // Then add to the same RG with new type (only auto accept guest)
        $regionalgroup->accounts()->attach($regionalgroupRequest->account, ['guest' => true, 'pilot' => true, 'controller' => true]);

        $regionalgroupRequest->delete();

        activity()
            ->causedBy($regionalgroupRequest->account)
            ->performedOn($regionalgroupRequest->account)
            ->log("Hat seinen Mitgliedschaftstypus in der Regionalgruppe {$regionalgroup->name} auf Gastmitglied verändert");
        activity()
            ->causedBy($regionalgroupRequest->account)
            ->performedOn($regionalgroupRequest->account)
            ->log("Die Regionalgruppeanfrage mit der RRID {$regionalgroupRequest->id} wurde automatisch akzeptiert!");

        $regionalgroupRequest->account->notify(new RequestAcceptedNotification($regionalgroup));
    }


	public function delete(Request $request, RegionalgroupRequest $regionalgroupRequest)
	{
        abort(422, 'disabled');
	}

}
