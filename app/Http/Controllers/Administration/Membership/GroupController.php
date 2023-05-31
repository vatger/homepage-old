<?php

namespace App\Http\Controllers\Administration\Membership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Membership\Group;
use App\Models\Forum\ForumGroup;

class GroupController extends Controller
{
    
	function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {

            if(!$this->account->hasPermission('administration.membership'))
                abort(403);

            return $next($request);
        });
    }

    /**
     * Get a list of all groups
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request)
    {
    	if($request->ajax()) {
    		return Group::all();
    	}
    }

    /**
     * Get details of a single group
     * @param  Request $request [description]
     * @param  Group   $group   [description]
     * @return [type]           [description]
     */
    public function show(Request $request, Group $group)
    {
        if($request->ajax()) {
            $group->loadMissing('users', 'permissions', 'forumgroups');
            return $group;
        }
    }

    /**
     * Adds a permission to a group
     * @param Request $request [description]
     * @param Group   $group   [description]
     */
    public function addPermission(Request $request, Group $group)
    {
        if($request->ajax()) {
            $group->assignPermissions($request->permission);
            return $this->returnUpdatedGroup($group);
        }
    }

    /**
     * Removes a permission from a group
     * @param  Request $request [description]
     * @param  Group   $group   [description]
     * @return [type]           [description]
     */
    public function removePermission(Request $request, Group $group)
    {
        if($request->ajax()) {
            $group->revokePermissions($request->permission);
            return $this->returnUpdatedGroup($group);
        }
    }

    /**
     * Add a user to the given group
     * @param Request $request [description]
     * @param Group   $group   [description]
     */
    public function addAccount(Request $request, Group $group)
    {
        if($request->ajax()) {
            $account = \App\Models\Membership\Account::find($request->cid);
            if($account != null) {
                $group->assignUser($account);
            }
            return $this->returnUpdatedGroup($group);
        }
    }

    /**
     * Removes a user from the given group
     * @param  Request $request [description]
     * @param  Group   $group   [description]
     * @return [type]           [description]
     */
    public function removeAccount(Request $request, Group $group)
    {
        if($request->ajax()) {
            $group->removeUser($request->cid);
            return $this->returnUpdatedGroup($group);
        }
    }

    /**
     * Assign a given forum group to this group
     * @param  Request    $request [description]
     * @param  Group      $group   [description]
     * @param  ForumGroup $fg      [description]
     * @return [type]              [description]
     */
    public function assignForumGroup(Request $request, Group $group)
    {
        if($request->ajax()) {
            $fg = ForumGroup::find($request->fg);
            if($fg != null)
                $group->forumgroups()->attach($fg);
            return $this->returnUpdatedGroup($group);
        }
    }

    /**
     * Detach the given forumgroup from this group
     * @param  Request $request [description]
     * @param  Group   $group   [description]
     * @return [type]           [description]
     */
    public function unassignForumGroup(Request $request, Group $group)
    {
        if($request->ajax()) {
            $fg = ForumGroup::find($request->fg);
            if($fg != null)
                $group->forumgroups()->detach($fg);
            return $this->returnUpdatedGroup($group);
        }
    }

    /**
     * Internal function to update a given group
     * and return it with updated data
     * 
     * @param  Group  $group [description]
     * @return [type]        [description]
     */
    protected function returnUpdatedGroup(Group $group)
    {
        $group->save();
        $group->refresh();
        $group->loadMissing('users', 'permissions', 'forumgroups');
        return $group;
    }

}
