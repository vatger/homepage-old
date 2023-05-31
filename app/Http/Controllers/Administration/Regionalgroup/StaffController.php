<?php


namespace App\Http\Controllers\Administration\Regionalgroup;


use App\Helpers\GroupHelper;
use App\Models\Membership\Account;
use App\Models\Regionalgroups\Regionalgroup;
use Illuminate\Http\Request;

class StaffController extends RegionalgroupController
{
    /**
     * Assigns a new mentor to the regionalgroup
     *
     * @param Request $request [description]
     * @param Regionalgroup $regionalgroup [description]
     * @return Account                       [description]
     */
    public function assignMentor(Request $request, Regionalgroup $regionalgroup)
    {
        if (!$request->ajax()) abort(403);

        $validated = $request->validate([
            'mentor' => 'required|exists:membership_accounts,id',
        ]);

        // We know, that the given "Mentor-Id" is actually an account
        // Now we need to check if the id is assigned to the regionalgroup
        $mentor = Account::find($validated['mentor']);

        if ($mentor != null) {
            if ($mentor->isMemberOfRegionalgroup($regionalgroup) || $mentor->isGuestOfRegionalgroup($regionalgroup)) {
                if (collect($regionalgroup->mentors)->where('id', $mentor->id)->count() > 0) abort(422, 'User is already part of the team.');
                $regionalgroup->mentors()->attach($mentor);
                $regionalgroup->save();
                $this->updateAccountPermissionsAndGroups($mentor);
                GroupHelper::assign($mentor, ['rg.mentor']);
                return $mentor;
            }
        }
        abort(422);
    }

    /**
     * Removes a mentor from a regionalgroup
     *
     * @param Request $request [description]
     * @param Regionalgroup $regionalgroup [description]
     * @param Account $mentor [description]
     * @return Account                       [description]
     */
    public function removeMentor(Request $request, Regionalgroup $regionalgroup, Account $mentor)
    {
        if (!$request->ajax()) abort(403);

        $regionalgroup->mentors()->detach($mentor);
        $regionalgroup->save();
        $this->updateAccountPermissionsAndGroups($mentor);
        return $mentor;
    }

    /**
     * Assigns a new navigator to the regionalgroup
     *
     * @param Request $request [description]
     * @param Regionalgroup $regionalgroup [description]
     * @return Account                       [description]
     */
    public function assignNavigator(Request $request, Regionalgroup $regionalgroup)
    {
        if (!$request->ajax()) abort(403);

        $validated = $request->validate([
            'navigator' => 'required|exists:membership_accounts,id',
        ]);

        // We know, that the given "Id" is actually an account
        // Now we need to check if the id is assigned to the regionalgroup
        $navigator = Account::find($validated['navigator']);

        if ($navigator != null) {
            if ($navigator->isMemberOfRegionalgroup($regionalgroup) || $navigator->isGuestOfRegionalgroup($regionalgroup)) {
                //check is member a navigator already
                if (collect($regionalgroup->navigators)->where('id', $navigator->id)->count() > 0) abort(422, 'User is already part of the team.');
                $regionalgroup->navigators()->attach($navigator);
                $this->updateAccountPermissionsAndGroups($navigator);
                $regionalgroup->save();
                GroupHelper::assign($navigator, ['rg.nav']);
                return $navigator;
            }
        }
        abort(422);
    }

    /**
     * Removes a navigator from a regionalgroup
     *
     * @param Request $request [description]
     * @param Regionalgroup $regionalgroup [description]
     * @param Account $navigator [description]
     * @return Account                       [description]
     */
    public function removeNavigator(Request $request, Regionalgroup $regionalgroup, Account $navigator)
    {
        if (!$request->ajax()) abort(403);

        $regionalgroup->navigators()->detach($navigator);
        $this->updateAccountPermissionsAndGroups($navigator);
        $regionalgroup->save();
        return $navigator;
    }

    /**
     * Assigns a new event team member to the regionalgroup
     *
     * @param Request $request [description]
     * @param Regionalgroup $regionalgroup [description]
     * @return Account                       [description]
     */
    public function assignEventler(Request $request, Regionalgroup $regionalgroup)
    {
        if (!$request->ajax()) abort(403);

        $validated = $request->validate([
            'eventler' => 'required|exists:membership_accounts,id',
        ]);

        // We know, that the given "Id" is actually an account
        // Now we need to check if the id is assigned to the regionalgroup
        $eventler = Account::find($validated['eventler']);

        if ($eventler != null) {
            if ($eventler->isMemberOfRegionalgroup($regionalgroup) || $eventler->isGuestOfRegionalgroup($regionalgroup)) {
                if (collect($regionalgroup->eventler)->where('id', $eventler->id)->count() > 0) abort(422, 'User is already part of the team.');
                $regionalgroup->eventler()->attach($eventler);
                $regionalgroup->save();
                $this->updateAccountPermissionsAndGroups($eventler);
                GroupHelper::assign($eventler, ['rg.event']);
                return $eventler;
            }
        }
        abort(422);
    }

    /**
     * Removes a event team member from a regionalgroup
     *
     * @param Request $request [description]
     * @param Regionalgroup $regionalgroup [description]
     * @param Account $eventler [description]
     * @return Account                       [description]
     */
    public function removeEventler(Request $request, Regionalgroup $regionalgroup, Account $eventler)
    {
        if (!$request->ajax()) abort(403);

        $regionalgroup->eventler()->detach($eventler);
        $regionalgroup->save();
        $this->updateAccountPermissionsAndGroups($eventler);
        return $eventler;
    }

    /**
     * Change the chief of a regionalgroup
     *
     * @param Request $request [description]
     * @param Regionalgroup $regionalgroup [description]
     * @return Account                       [description]
     */
    public function assignChief(Request $request, Regionalgroup $regionalgroup)
    {
        if (!$request->ajax()) abort(403);

        if ($request->has('newChief') && $request->newChief == -1) {
            //try to remove permisson group of old chief
            GroupHelper::revokeViaId($regionalgroup->chief_id, ['rg.leitung']);

            $regionalgroup->chief_id = null;
            $regionalgroup->save();
            return null;
        }
        $validated = $request->validate([
            'newChief' => 'required|exists:membership_accounts,id',
        ]);

        // The new chief must be at lease a full member of the regionalgroup
        $newChief = Account::find($validated['newChief']);

        if ($newChief != null && $newChief->isMemberOfRegionalgroup($regionalgroup) || $newChief->isGuestOfRegionalgroup($regionalgroup)) {
            //try to remove permisson group of old chief
            GroupHelper::revokeViaId($regionalgroup->chief_id, ['rg.leitung']);

            $regionalgroup->chief()->associate($newChief);
            $regionalgroup->save();

            //try to give permisson group to new chief
            GroupHelper::assignViaId($regionalgroup->chief_id, ['rg.leitung']);

            return $newChief;
        }
        abort(422);
    }

    /**
     * Change the deputy of the regionalgroup
     *
     * @param Request $request [description]
     * @param Regionalgroup $regionalgroup [description]
     * @return Account                       [description]
     */
    public function assignDeputy(Request $request, Regionalgroup $regionalgroup)
    {
        if (!$request->ajax()) abort(403);

        if ($request->has('newDeputy') && $request->newDeputy == -1) {
            //try to remove permisson group of old deputy
            GroupHelper::revokeViaId($regionalgroup->deputy_id, ['rg.leitung']);

            $regionalgroup->deputy_id = null;
            $regionalgroup->save();
            return null;
        }
        $validated = $request->validate([
            'newDeputy' => 'required|exists:membership_accounts,id',
        ]);

        // The new deputy must be at lease a full member of the regionalgroup
        $newDeputy = Account::find($validated['newDeputy']);

        if ($newDeputy != null && $newDeputy->isMemberOfRegionalgroup($regionalgroup) || $newDeputy->isGuestOfRegionalgroup($regionalgroup)) {
            //try to remove permisson group of old deputy
            GroupHelper::revokeViaId($regionalgroup->deputy_id, ['rg.leitung']);

            $regionalgroup->deputy()->associate($newDeputy);
            $regionalgroup->save();

            //try to give permisson group to new deputy
            GroupHelper::assignViaId($regionalgroup->deputy_id, ['rg.leitung']);

            return $newDeputy;
        }

        abort(422);
    }

}
