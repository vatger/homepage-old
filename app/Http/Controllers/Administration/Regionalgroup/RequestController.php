<?php


namespace App\Http\Controllers\Administration\Regionalgroup;

use App\Libraries\XenBridge;
use App\Models\Regionalgroups\Regionalgroup;
use App\Models\Regionalgroups\RegionalgroupRequest;
use App\Models\Regionalgroups\RegionalgroupTemplate;
use App\Notifications\Administration\Regionalgroups\RequestAcceptedNotification;
use App\Notifications\Administration\Regionalgroups\RequestDeniedNotification;
use Illuminate\Http\Request;

class RequestController extends RegionalgroupController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Accept and handle a regionalgroup request
     *
     * @param  Request              $request              [description]
     * @param  Regionalgroup        $regionalgroup        [description]
     * @param  RegionalgroupRequest $regionalgroupRequest [description]
     * @return [type]                                     [description]
     */
    public function acceptRequest(Request $request, Regionalgroup $regionalgroup, RegionalgroupRequest $regionalgroupRequest)
    {
        abort(422, 'disabled');
    }

    /**
     * Just deny and delete a regionalgroup request
     *
     * @param  Request              $request              [description]
     * @param  Regionalgroup        $regionalgroup        [description]
     * @param  RegionalgroupRequest $regionalgroupRequest [description]
     * @return [type]                                     [description]
     */
    public function denyRequest(Request $request, Regionalgroup $regionalgroup, RegionalgroupRequest $regionalgroupRequest)
    {
        abort(422, 'disabled');
    }

    /**
     * Handle any form of join requests
     *
     * @param  Regionalgroup        $regionalgroup        [description]
     * @param  RegionalgroupRequest $regionalgroupRequest [description]
     * @return [type]                                     [description]
     */
    private function handleJoinRequest(Regionalgroup $regionalgroup, RegionalgroupRequest $regionalgroupRequest, $notificationDetails = null)
    {
        // If the user wants to join as a guest, we can accept regardless of any other regionalgroup assignments
        // But if the user is a fullmember anywhere else and wants to join as a member at this regionalgroup, we must deny.
        if ($regionalgroupRequest->type == 'guest') {
            /*
            $asPilot = $regionalgroupRequest->as == 'pilot';
            $asController = $regionalgroupRequest->as == 'controller';
            if ($regionalgroupRequest->as == 'both') {
                $asPilot = $asController = true;
            }
            */
            $regionalgroup->accounts()->attach($regionalgroupRequest->account, ['guest' => true, 'pilot' => true, 'controller' => true]);

            activity()
                ->causedBy($this->account)
                ->performedOn($regionalgroupRequest->account)
                ->log("Ist der Regionalgruppe {$regionalgroup->name} als Gastmitglied beigetreten!");

            $regionalgroupRequest->account->notify(new RequestAcceptedNotification($regionalgroup, $notificationDetails));

            return true;
        }
        if ($regionalgroupRequest->type == 'member') {
            // First let's check for any other regionalgroup membership
            $regionalgroups = Regionalgroup::all();
            $targetAccount = $regionalgroupRequest->account;

            $isMemberAnywhere = false;
            foreach ($regionalgroups as $rg) {
                if ($targetAccount->isMemberOfRegionalgroup($rg)) {
                    $isMemberAnywhere = true;
                    break;
                }
            }
            if ($isMemberAnywhere) {
                return false;
            }

            // Now let's put the user into the group
            /*
            $asPilot = $regionalgroupRequest->as == 'pilot';
            $asController = $regionalgroupRequest->as == 'controller';
            if ($regionalgroupRequest->as == 'both') {
                $asPilot = $asController = true;
            }
            */
            $regionalgroup->accounts()->attach($targetAccount, ['guest' => false, 'pilot' => true, 'controller' => true]);

            activity()
                ->causedBy($this->account)
                ->performedOn($targetAccount)
                ->log("Ist der Regionalgruppe {$regionalgroup->name} als Vollmitglied beigetreten!");

            $regionalgroupRequest->account->notify(new RequestAcceptedNotification($regionalgroup, $notificationDetails));

            return true;
        }
        return false;
    }

    /**
     * Handle any kind of leave request
     *
     * @param  Regionalgroup        $regionalgroup        [description]
     * @param  RegionalgroupRequest $regionalgroupRequest [description]
     * @return [type]                                     [description]
     */
    private function handleLeaveRequest(Regionalgroup $regionalgroup, RegionalgroupRequest $regionalgroupRequest, $notificationDetails = null)
    {
        // first kick the user out of the regionalgroup
        $regionalgroup->accounts()->detach($regionalgroupRequest->account);
        $regionalgroup->save();

        $this->updateAccountPermissionsAndGroups($regionalgroupRequest->account);

        $regionalgroupRequest->account->notify(new RequestAcceptedNotification($regionalgroup, $notificationDetails));

        activity()
            ->causedBy($this->account)
            ->performedOn($regionalgroupRequest->account)
            ->log("Hat die Regionalgruppe {$regionalgroup->name} verlassen!");


        return true;
    }

    /**
     * Handle any kind of change request
     *
     * @param  Regionalgroup        $regionalgroup        [description]
     * @param  RegionalgroupRequest $regionalgroupRequest [description]
     * @return [type]                                     [description]
     */
    private function handleChangeRequest(Regionalgroup $regionalgroup, RegionalgroupRequest $regionalgroupRequest, $notificationDetails = null)
    {
        $regionalgroup->accounts()->detach($regionalgroupRequest->account);
        $regionalgroup->save();

        $this->updateAccountPermissionsAndGroups($regionalgroupRequest->account);

        // Then add to the same RG with new type
        $asGuest = $regionalgroupRequest->type == 'guest';
        $regionalgroup->accounts()->attach($regionalgroupRequest->account, ['guest' => $asGuest, 'pilot' => true, 'controller' => true]);

        activity()
            ->causedBy($this->account)
            ->performedOn($regionalgroupRequest->account)
            ->log("Hat seinen Mitgliedschaftstypus in der Regionalgruppe {$regionalgroup->name} auf " . ($asGuest ? 'Gastmitglied' : 'Vollmitglied') . " verändert!");

        $regionalgroupRequest->account->notify(new RequestAcceptedNotification($regionalgroup, $notificationDetails));

        return true;
    }

}
