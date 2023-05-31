<?php

namespace App\Http\Controllers\Administration\Regionalgroup;

use App\Helpers\GroupHelper;
use App\Http\Controllers\Controller;
use App\Libraries\Gitlab;
use App\Models\Membership\Account;
use App\Models\Regionalgroups\Regionalgroup;
use App\Models\Regionalgroups\RegionalgroupRequest;
use App\Notifications\Administration\Regionalgroups\RequestAcceptedNotification;
use App\Notifications\Administration\Regionalgroups\RequestDeniedNotification;
use Illuminate\Http\Request;

class RegionalgroupController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

            if (!$this->account->hasAnyPermission('administration.regionalgroup', 'administration.regionalgroup.rg')) {
                abort(403);
            }

            return $next($request);
        });
    }

    /**
     * Get all regionalgroups a user can administrate
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $regionalgroups = Regionalgroup::select('id','name','email','fir_id','chief_id','deputy_id')->with('fir:id,name')->orderBy('name', 'ASC')->get();
            if ($this->account->hasPermission('administration.regionalgroup')) {
                return $regionalgroups;
            } elseif ($this->account->hasPermission('administration.regionalgroup.rg')) {
                // Only if the account is in the staff team we grant access
                return $regionalgroups->filter(function ($rg) {
                    return $this->account->id == $rg->chief_id ||  $this->account->id == $rg->deputy_id;
                });

            }
        }
        abort(403);
    }

    /**
     * Get a detailed presentation of a regionalgroup
     *
     * @param  Request       $request       [description]
     * @param  Regionalgroup $regionalgroup [description]
     * @return [type]                       [description]
     */
    public function show(Request $request, Regionalgroup $regionalgroup)
    {
        if (!$request->ajax()) abort(403);
        if ($this->hasRGPermission($regionalgroup)){
            $regionalgroup->loadMissing('fir', 'accounts', 'accounts.data', 'chief', 'deputy', 'mentors', 'navigators', 'eventler', 'requests.account', 'requests.account.setting', 'requests.account.data', 'templates');
            return $regionalgroup;
        }
        abort(422);
    }

    /**
     * Update forum group assignments of this regionalgroup
     *
     * @param  Request       $request       [description]
     * @param  Regionalgroup $regionalgroup [description]
     * @return [type]                       [description]
     */
    public function update(Request $request, Regionalgroup $regionalgroup)
    {
        if ($request->ajax()) {
            // As this permission is only for systemadministrators and only those shall be able to set these!
            if ($this->account->hasPermission('administration.regionalgroup')) {

                $validated = $request->validate([
                    'staff_group_id' => 'required|integer|exists:forumgroups,id',
                    'voting_group_id' => 'required|integer|exists:forumgroups,id',
                    'mentor_group_id' => 'required|integer|exists:forumgroups,id',
                    'navler_group_id' => 'required|integer|exists:forumgroups,id',
                    'eventler_group_id' => 'required|integer|exists:forumgroups,id',
                    'member_group_id' => 'required|integer|exists:forumgroups,id',
                    'guest_group_id' => 'required|integer|exists:forumgroups,id',
                ]);

                $regionalgroup->staff_group_id = $validated['staff_group_id'];
                $regionalgroup->voting_group_id = $validated['voting_group_id'];
                $regionalgroup->mentor_group_id = $validated['mentor_group_id'];
                $regionalgroup->navler_group_id = $validated['navler_group_id'];
                $regionalgroup->eventler_group_id = $validated['eventler_group_id'];
                $regionalgroup->member_group_id = $validated['member_group_id'];
                $regionalgroup->guest_group_id = $validated['guest_group_id'];

                $regionalgroup->save();

                activity()
                   ->causedBy($this->account)
                   ->performedOn($regionalgroup)
                   ->log("Hat die Regionalgruppe {$regionalgroup->name} verändert!");

                return $regionalgroup;
            }
        }
        abort(403);
    }

    /**
     * Removes an account from the regionalgroup
     *
     * @param  Request       $request       [description]
     * @param  Regionalgroup $regionalgroup [description]
     * @param  Account       $account       [description]
     * @return int                          [description]
     */
    public function removeAccount(Request $request, Regionalgroup $regionalgroup, Account $account)
    {
        if (!$request->ajax()) abort(403);

        $regionalgroup->mentors()->detach($account);
        $regionalgroup->navigators()->detach($account);
        $regionalgroup->eventler()->detach($account);

        $regionalgroup->accounts()->detach($account);
        $regionalgroup->save();

        $this->updateAccountPermissionsAndGroups($account);

        activity()
            ->causedBy($this->account)
            ->performedOn($account)
            ->log("Wurde aus der Regionalgruppe {$regionalgroup->name} entfernt!");

        return $account->id;
    }



    protected function updateAccountPermissionsAndGroups(Account $account) {
        // Then find out if the account has any remaining positions within any regionalgroup
        $regionalgroups = Regionalgroup::all();
        $isMemberAnywhere = false;
        $isStaffAnywhere = false;
        $isMentorAnywhere = false;
        $isNavAnywhere = false;
        $isEventAnywhere = false;
        foreach ($regionalgroups as $rg) {
            if ($account->isMemberOfRegionalgroup($rg)) {
                $isMemberAnywhere = true;
                if($rg->chief_id == $account->id
                || $rg->deputy_id == $account->id) {
                    $isStaffAnywhere = true;
                }
                $isMentorAnywhere = $account->isMentorOfRegionalgroup($rg);
                $isNavAnywhere = $account->isNavigatorOfRegionalgroup($rg);
                $isEventAnywhere = $account->isEventlerOfRegionalgroup($rg);
                break;
            }
        }
        // Now regarding to the standings. Remove permissions and group assignments as needed
        if(!$isMemberAnywhere) {
            // Not a member anywhere, but still might be guest
            if($isStaffAnywhere) { // Can not be is a staff group as guest only
                GroupHelper::revoke($account, ['rg.leitung']);
            }
        } else {
            // Member anywhere
            if(!$isStaffAnywhere) {
                GroupHelper::revoke($account, ['rg.leitung']);
            }
        }
        if(!$isMentorAnywhere) {
            GroupHelper::revoke($account, ['rg.mentor']);
        }
        if(!$isNavAnywhere) {
            GroupHelper::revoke($account, ['rg.nav']);
        }
        if(!$isEventAnywhere) {
            GroupHelper::revoke($account, ['rg.event']);
        }
        // do the gitlab stuff
        //$gitlab = new Gitlab();
        //$gitlab->checkNAVAssignments($account);
    }

    /**
     * Current Account can manage all RGs or is RG Chief/Deputy
     *
     * @param  Regionalgroup $regionalgroup [description]
     * @return bool
     */
    protected function hasRGPermission(Regionalgroup $regionalgroup): bool
    {
        if ($this->account->hasPermission('administration.regionalgroup')) return true;
        if ($this->account->hasPermission('administration.regionalgroup.rg')){
            if ($this->account->id == $regionalgroup->chief_id || $this->account->id == $regionalgroup->deputy_id) return true;
        }
        return false;
    }

}
