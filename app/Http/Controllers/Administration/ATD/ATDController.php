<?php

namespace App\Http\Controllers\Administration\ATD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\ATD\SoloApprovedNotification;
use App\Notifications\ATD\SoloDeniedNotification;
use App\Notifications\ATD\SoloExtendedNotification;
use App\Notifications\ATD\SoloModifiedNotification;

class ATDController extends Controller
{
    
	function __construct()
	{
		parent::__construct();

		$this->middleware(function ($request, $next) {

			if(!$this->account->hasPermission('administration.atd'))
				abort(403);

			return $next($request);
		});
	}

	public function index(Request $request)
	{
		if(!$request->ajax()) abort(403);
	}

	/**
	 * Get all solo endorsements inclusive requests
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function solos(Request $request)
	{
		if(!$request->ajax()) abort(403);

		$solos = \App\Models\ATD\SoloEndorsement::orderBy('ends_at', 'ASC')
            ->with('candidate', 'phase', 'station')
            ->get();

        $regionalgroups = \App\Models\Regionalgroups\Regionalgroup::all();

        $stations = \App\Models\Navigation\Station::orderBy('name', 'ASC')->get();
        $phases = \App\Models\ATD\SoloPhase::all();

        return response()->json(
            [
                'solos' => $solos,
                'regionalgroups' => $regionalgroups,
                'stations' => $stations,
                'phases' => $phases,
                'success' => true,
            ],
            200
        );
	}

	/**
     * Approve or revoke a solo endorsement.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function approveSolo(Request $request)
    {
    	if(!$request->ajax()) abort(403);

        $validated = $request->validate(
            [
                'solo' => 'required|exists:uts_atd_solo_clearances,id',
                'approved' => 'required|boolean',
            ]
        );

        $solo = \App\Models\ATD\SoloEndorsement::find($validated['solo']);
        $solo->loadMissing('candidate', 'station');
        $solo->approved = $validated['approved'];
        $solo->save();

        // Notify the candidate and also provide feedback for the authorized user
        if ($solo->approved) {
        	$solo->candidate->notify(new SoloApprovedNotification($solo->station->ident));
        } else {
        	$solo->candidate->notify(new SoloDeniedNotification($solo->station->ident));
        }

        return response()->json(
            [
                'success' => true,
            ],
            200
        );
    }

    /**
     * Deletes a solo endorsement.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function deleteSolo(Request $request)
    {
    	if(!$request->ajax()) abort(403);

        $validated = $request->validate(
            [
                'solo' => 'required|exists:uts_atd_solo_clearances,id',
            ]
        );

        $solo = \App\Models\ATD\SoloEndorsement::find($validated['solo']);
        $solo->loadMissing('candidate', 'station');

        $c = $solo->candidate;
        $s = $solo->station->ident;

        $solo->delete();

        $c->notify(new SoloDeniedNotification($s));

        return response()->json(
            [
                'success' => true,
            ],
            200
        );
    }

    /**
     * Extends a solo by 30 days.
     *
     * @param Request                             $request [description]
     * @param \App\Models\ATD\SoloEndorsement $solo    [description]
     *
     * @return [type] [description]
     */
    public function extendSolo(Request $request, \App\Models\ATD\SoloEndorsement $solo)
    {
    	if(!$request->ajax()) abort(403);

        $validated = $request->validate(
            [
                'extension' => 'required|integer',
            ]
        );

        $solo->loadMissing('candidate', 'station');

        $solo->extensions = $validated['extension'];
        $solo->ends_at = $solo->calculateEndDate($solo->ends_at, true);
        $solo->save();

        $solo->candidate->notify(new SoloExtendedNotification($solo->station->ident));

        return response()->json(
            [
                'success' => true,
            ],
            200
        );
    }

    /**
     * Switch the station an endorsement is valid for.
     *
     * @param Request                             $request [description]
     * @param \App\Models\ATD\SoloEndorsement $solo    [description]
     *
     * @return [type] [description]
     */
    public function switchStation(Request $request, \App\Models\ATD\SoloEndorsement $solo)
    {
    	if(!$request->ajax()) abort(403);

        $validated = $request->validate(
            [
                'station' => 'required|exists:navigation_stations,id',
            ]
        );

        $solo->loadMissing('candidate', 'station');

    	$solo->candidate->notify(new SoloDeniedNotification($solo->station->ident));
    
        $solo->station_id = $validated['station'];
        $solo->save();

        $solo->refresh();
        $solo->loadMissing('candidate', 'station');
    	$solo->candidate->notify(new SoloApprovedNotification($solo->station->ident));

        return response()->json(
            [
                'success' => true,
            ],
            200
        );
    }

    /**
     * Forwards a soloendorsement to a new phase.
     * Recalculating the end date based upon the current end date.
     *
     * @param Request                             $request [description]
     * @param \App\Models\ATD\SoloEndorsement $solo    [description]
     *
     * @return [type] [description]
     */
    public function forwardPhase(Request $request, \App\Models\ATD\SoloEndorsement $solo)
    {
    	if(!$request->ajax()) abort(403);

        $validated = $request->validate(
            [
                'phase' => 'required|exists:uts_atd_solophases,id',
            ]
        );

        $solo->loadMissing('candidate', 'station');

        $solo->solophase_id = $validated['phase'];
        $solo->ends_at = $solo->calculateEndDate($solo->ends_at, false);
        if (false !== $solo->ends_at) {
            $solo->save();

            $solo->candidate->notify(new SoloModifiedNotification($solo->station->ident));

            return response()->json(
                [
                    'success' => true,
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ],
                200
            );
        }
    }

    /**
     * Switch the phase of an endorsement completely.
     * Recalculating the new end date based on the original begin.
     *
     * @param Request                             $request [description]
     * @param \App\Models\ATD\SoloEndorsement $solo    [description]
     *
     * @return [type] [description]
     */
    public function switchPhase(Request $request, \App\Models\ATD\SoloEndorsement $solo)
    {
    	if(!$request->ajax()) abort(403);

        $validated = $request->validate(
            [
                'phase' => 'required|exists:uts_atd_solophases,id',
            ]
        );

        $solo->loadMissing('candidate', 'station');

        $solo->solophase_id = $validated['phase'];
        $solo->ends_at = $solo->calculateEndDate($solo->begins_at, false);
        if (false !== $solo->ends_at) {
            $solo->save();

            $solo->candidate->notify(new SoloModifiedNotification($solo->station->ident));

            return response()->json(
                [
                    'success' => true,
                ],
                200
            );
        } else {
            
            return response()->json(
                [
                    'success' => false,
                ],
                200
            );
        }
    }

    /**
     * Create a new solo entry
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function createSolo(Request $request)
    {
    	if(!$request->ajax()) abort(403);

    	$validated = $request->validate([
    		'newSolo.cid' => 'required|exists:membership_accounts,id',
    		'newSolo.begin' => 'required',
    		'newSolo.phase' => 'required|exists:uts_atd_solophases,id',
    		'newSolo.station' => 'required|exists:navigation_stations,id',
    	]);

    	$newSolo = new \App\Models\ATD\SoloEndorsement;
    	$newSolo->station_id = $validated['newSolo']['station'];
    	$newSolo->solophase_id = $validated['newSolo']['phase'];
    	$newSolo->candidate_id = $validated['newSolo']['cid'];
    	$newSolo->begins_at = \Carbon\Carbon::createFromFormat('d.m.Y', $validated['newSolo']['begin']);
    	$newSolo->ends_at = $newSolo->calculateEndDate($newSolo->begins_at);
    	$newSolo->approved = true;
    	$newSolo->save();
    	$newSolo->refresh();
    	$newSolo->loadMissing('candidate', 'station');
    	$newSolo->candidate->notify(new SoloApprovedNotification($newSolo->station->ident));

    	return response()->json(
            [
            	'newSolo' => $newSolo,
                'success' => true,
            ],
            200
        );
    }

}
