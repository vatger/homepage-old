<?php

namespace App\Http\Controllers\Administration\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Forum\ForumGroup;

class ForumgroupController extends Controller
{
    
	function __construct() {
		parent::__construct();

		$this->middleware(function ($request, $next) {

            if(!$this->account->hasPermission('administration.forumgroups'))
                abort(403);

            return $next($request);
        });
	}

	/**
	 * Gets all forumgroups known to our local system
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function index(Request $request) {
		if(!$request->ajax()) return abort(403);

		$forumgroups = ForumGroup::all();

		return $forumgroups;
	}

	/**
	 * Adds a new forumgroup to our local system
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function create(Request $request) {
		$validated = $request->validate([
			'name' => 'required|string',
			'id' => 'required|numeric',
		]);

		/**
		 * If there is already a group with that id, we must not continue
		 */
		if(ForumGroup::where('forum_id', $validated['id'])->exists()) {
			$this->account->notify(new \App\Notifications\Administration\Forum\GroupAlreadyExistsNotification($validated['id']));
			return response()->json(false);
		}

		$fg = new ForumGroup;
		$fg->forum_id = $validated['id'];
		$fg->name = $validated['name'];
		$fg->save();

		return $fg;
	}

	public function remove(Request $request, ForumGroup $fg)
	{
		$fid = $fg->id;

		$fg->groups()->detach();

		$fg->delete();

		return $fid;
	}

}
