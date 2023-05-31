<?php

namespace App\Http\Controllers\Administration\Membership;

use App\Http\Controllers\Controller;
use App\Libraries\TeamSpeakWebquery;
use App\Libraries\XenBridge;
use App\Models\Membership\Account;
use App\Models\Membership\Account\Ban;
use App\Models\Membership\Account\Note;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Throwable;


class AccountController extends Controller
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
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $allAccounts = Account::orderBy('lastname', 'ASC')->get();

        return $allAccounts;
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @return View
     */
    public function show(Account $account)
    {
        $actionsOnAccount = Activity::forSubject($account)->get();
        $actionsCausedBy = Activity::causedBy($account)->get();
        $account->loadMissing('data', 'setting', 'regionalgroups', 'notes.author', 'bans.author', 'teamspeakRegistrations');
        return ['account' => $account, 'actions' => $actionsOnAccount->merge($actionsCausedBy)];
    }

    /**
     * @param Request $request
     * @param Account $account
     * @return RedirectResponse
     */
    public function addNote(Request $request, Account $account)
    {

        $validated = $request->validate(
            [
                'newNote' => 'required|string',
            ]
        );

        // Create a new note and assign to the account
        $newNote = new Note([
            'note' => $validated['newNote'],
            'author_id' => $this->account->id,
            'account_id' => $account->id,
        ]);
        $newNote->save();

        if($request->ajax()){
            return $newNote;
        }
        return redirect()->back();
    }

    /**
     * Removes a note from the user
     * @param  Request $request [description]
     * @param  Account $account [description]
     * @param  Note    $note    [description]
     * @return [type]           [description]
     */
    public function removeNote(Request $request, Account $account, Note $note)
    {
        if($note->account_id == $account->id) {
            $note->delete();
        }

        if($request->ajax()) {
            return true;
        }
        return redirect()->back();
    }

    /**
     * Banns the user for a given reason and for a given timeframe
     * The timeframe can be indefinite by marking the permanent flat to true
     *
     * @param Request $request [description]
     * @param Account $account [description]
     */
    public function addBan(Request $request, Account $account)
    {
        if(!$request->ajax()) {
            return;
        }
        $validated = $request->validate(
            [
                'permanent' => 'required|boolean',
                'till' => 'required_without:permanent|nullable|date',
                'reason' => 'required|string',
                'homepage' => 'required|boolean',
                'forum' => 'required|boolean',
                'teamspeak' => 'required|boolean',
            ]
        );

        $newBan = new Ban([
            'reason' => $validated['reason'],
            'author_id' => $this->account->id,
            'account_id' => $account->id,
            'homepage' => $validated['homepage'],
            'forum' => $validated['forum'],
            'teamspeak' => $validated['teamspeak'],
        ]);

        if($validated['permanent']) {
            // We need to create a permanent ban here
            $newBan->permanent = true;
            $newBan->banned_till = null;
        } else {
            // we have an end date given
            $newBan->permanent = false;
            $newBan->banned_till = Carbon::createFromFormat('Y-m-d', $validated['till'], 'UTC');
            $newBan->banned_till->setTime(23,59,59);
        }

        $newBan->save();

        // Update the forum
        try{
            if ($account->is_currently_forum_banned) {
                XenBridge::banForumAccount($account);
            }
        } catch (Throwable $th) {
            //throw $th;
        }


        // Let the TS know that user may be banned
        TeamSpeakWebquery::checkAccount($account);

        return $newBan;

    }

    /**
     * Removes a ban from the user
     * @param  Request $request [description]
     * @param  Account $account [description]
     * @param  Ban     $ban     [description]
     * @return [type]           [description]
     */
    public function removeBan(Request $request, Account $account, Ban $ban)
    {
        if($ban->account_id == $account->id) {
            $ban->delete();
        }
        if($request->ajax())
        {
            return true;
        }
        return redirect()->back();
    }
}
