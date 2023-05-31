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
		if($request->ajax()) {
			$validated = $request->validate([
				'newRequest.regionalgroup' => 'required|exists:regionalgroups_regionalgroups,id',
				'newRequest.reason' => 'required|string|min:25|max:150',
				'newRequest.topic' => 'required|in:join,change,leave',
				'newRequest.type' => 'required|in:member,guest,none',
				// 'newRequest.as' => 'required|in:controller,pilot,both',
				//'newRequest.destination' => 'nullable|required_if:newRequest.topic,change|exists:regionalgroups_regionalgroups,id',
			]);

			if(RegionalgroupRequest::where('account_id', $this->account->id)->where('regionalgroup_id', $validated['newRequest']['regionalgroup'])->exists()) {
                //already has one request to the rg
				$this->account->notify(new RegionalgroupRequestExistsNotification);
				abort(422, trans('regionalgroup.request.errors.already_request_to_rg'));
			}
            if($validated['newRequest']['type'] == 'member' && $this->account->regionalgroups()->wherePivot('guest', false)->exists()){
                //is fullmember already otherwhere
                abort(422, trans('regionalgroup.request.errors.already_fullmember_otherwhere'));
            }

            if($validated['newRequest']['type'] == 'member' && RegionalgroupRequest::where('account_id', $this->account->id)->where('type','member')->exists()){
                //wants fullmember already otherwhere
                abort(422, trans('regionalgroup.request.errors.already_request_fullmember_otherwhere'));
            }

			$rg = Regionalgroup::query()->find($validated['newRequest']['regionalgroup']);

			if (!($validated['newRequest']['topic'] == 'change' || $validated['newRequest']['topic'] == 'leave') && ($this->account->isMemberOfRegionalgroup($rg) || $this->account->isGuestOfRegionalgroup($rg))) {
                // is already part of this rg
				abort(422, trans('regionalgroup.request.errors.already_part_of_rg'));
            }
            if($validated['newRequest']['topic'] == 'change' && !($this->account->isMemberOfRegionalgroup($rg) || $this->account->isGuestOfRegionalgroup($rg))){
                // wants type changed but is no member/guest of this rg
                abort(422, trans('regionalgroup.request.errors.change_but_no_member'));
            }
            if ($validated['newRequest']['topic'] == 'change' && $validated['newRequest']['type'] == 'member' && $this->account->isMemberOfRegionalgroup($rg)){
                // wants type changed but is already member
                abort(422, trans('regionalgroup.request.errors.change_but_is_member'));
            }
            if ($validated['newRequest']['topic'] == 'change' && $validated['newRequest']['type'] == 'guest' && $this->account->isGuestOfRegionalgroup($rg)){
                // wants type changed but is already guest
                abort(422, trans('regionalgroup.request.errors.change_but_is_guest'));
            }

            if ($validated['newRequest']['topic'] == 'leave' && !($this->account->isMemberOfRegionalgroup($rg) || $this->account->isGuestOfRegionalgroup($rg))){
                // wants leave but is no member/guest
                abort(422, trans('regionalgroup.request.errors.leave_but_is_member'));
            }

			$newRequest = new RegionalgroupRequest();
			$newRequest->regionalgroup_id = $validated['newRequest']['regionalgroup'];
            $newRequest->account_id = $this->account->id;
            $newRequest->topic = $validated['newRequest']['topic'];
            $newRequest->reason = $validated['newRequest']['reason'];
            if ($validated['newRequest']['topic'] == 'leave' && $this->account->isMemberOfRegionalgroup($rg)){
                $newRequest->type = 'member';
            } else if ($validated['newRequest']['topic'] == 'leave' && $this->account->isGuestOfRegionalgroup($rg)){
                $newRequest->type = 'guest';
            } else {
                $newRequest->type = $validated['newRequest']['type'];
            }
			// $newRequest->as = $validated['newRequest']['as'];
			$newRequest->as = 'both';
			$newRequest->save();
			$newRequest->loadMissing('regionalgroup');

            //if the user wants to change to guest membership no appoval is needed
            if ($newRequest->topic == 'change' && $newRequest->type == 'guest'){
                $this->_autoAcceptChangeRequest($newRequest);
            }

            $newRequest->regionalgroup->setAppends([]);
			return $newRequest;
		}
		abort(403);
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
		if($request->ajax()) {
			$rrid = $regionalgroupRequest->id;
			$regionalgroupRequest->delete();

			return $rrid;
		}
		abort(403);
	}

}
