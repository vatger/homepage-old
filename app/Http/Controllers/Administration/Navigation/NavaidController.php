<?php

namespace App\Http\Controllers\Administration\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Navigation\Navaid;

class NavaidController extends Controller
{

	function __construct()
	{
		parent::__construct();

		$this->middleware(function($request, $next) {

			if(!$this->account->hasAnyPermission('administration.navigation', 'administration.navigation.rg'))
				abort(403);

			return $next($request);
		});
	}

	public function index(Request $request)
	{
		if($request->ajax()) {
			return Navaid::all();
		}
	}

	public function create(Request $request)
	{
		if($request->ajax()) {
			// Create a new navaid
			$validated = $request->validate(
	            [
	                'newNavaid.name' => 'required|string',
	                'newNavaid.type' => 'required|in:1,2,3,4,5,6',
	                'newNavaid.ident' => 'required|string|max:5',
	                'newNavaid.heading' => ['nullable', 'required_if:editNavaid.type,'.Navaid::TYPE_ILS, new \App\Rules\Navigation\HeadingRule],
	                'newNavaid.remarks' => 'nullable|string',
					'newNavaid.frequency' => ['required', new \App\Rules\Navigation\FrequencyRule],
	                'newNavaid.frequency_band' => 'required|in:1,2',
	            ]
	        );

			$navaid = new Navaid;
	        $navaid->name = $validated['newNavaid']['name'];
	        $navaid->type = $validated['newNavaid']['type'];
	        $navaid->ident = $validated['newNavaid']['ident'];
	        $navaid->heading = $validated['newNavaid']['heading'] ?? '';
	        $navaid->remarks = $validated['newNavaid']['remarks'] ?? '';
	        $navaid->frequency = $this->_modifyFrequency($validated['newNavaid']['frequency']);
	        $navaid->frequency_band = $validated['newNavaid']['frequency_band'];

			$navaid->save();

			$this->account->notify(new \App\Notifications\Administration\Navigation\NavaidCreatedNotification($navaid));

			return $navaid;
		}
	}

	public function update(Request $request, Navaid $navaid)
	{
		if($request->ajax()) {
			// Update navaid data
			$validated = $request->validate(
	            [
	                'editNavaid.name' => 'required|string',
	                'editNavaid.type' => 'required|in:1,2,3,4,5,6',
	                'editNavaid.ident' => 'required|string|max:5',
	                'editNavaid.heading' => ['nullable', 'required_if:editNavaid.type,'.Navaid::TYPE_ILS, new \App\Rules\Navigation\HeadingRule],
	                'editNavaid.remarks' => 'nullable|string',
	                'editNavaid.frequency' => ['required', new \App\Rules\Navigation\FrequencyRule],
	                'editNavaid.frequency_band' => 'required|in:1,2',
	            ]
	        );

	        $navaid->name = $validated['editNavaid']['name'];
	        $navaid->type = $validated['editNavaid']['type'];
	        $navaid->ident = $validated['editNavaid']['ident'];
	        $navaid->heading = $validated['editNavaid']['heading'] ?? '';
	        $navaid->remarks = $validated['editNavaid']['remarks'] ?? '';
	        $navaid->frequency = $this->_modifyFrequency($validated['editNavaid']['frequency']);
	        $navaid->frequency_band = $validated['editNavaid']['frequency_band'];

			$navaid->save();

			$this->account->notify(new \App\Notifications\Administration\Navigation\NavaidUpdatedNotification($navaid));

			return $navaid;
		}
	}

	public function delete(Request $request, Navaid $navaid)
	{
		if($request->ajax()) {
			$navaid->aerodromes()->detach();

			$nid = $navaid->id;
			$nString = $navaid->name . ' | ' . $navaid->ident;

			$navaid->delete();

			$this->account->notify(new \App\Notifications\Administration\Navigation\NavaidDeletedNotification($nString));

			return $nid;
		}
	}

	private function _modifyFrequency($fs)
	{
		// Modify the frequency to be perfect
        $frequencyString = $fs;
        $frequency = 000.000;
        if (7 == strlen($frequencyString)) {
            if (!strpos($frequencyString, '.')) {
                $frequency = floatval(substr($frequencyString, 0, 3).'.'.substr($frequencyString, 3, 3));
            } else {
                $frequency = floatval($frequencyString);
            }
        } elseif (6 == strlen($frequencyString)) {
            if (!strpos($frequencyString, '.')) {
                $frequency = floatval(substr($frequencyString, 0, 3).'.'.substr($frequencyString, 3));
            } else {
                $frequency = floatval($frequencyString);
            }
        } elseif (strlen($frequencyString) < 6 && strlen($frequencyString) >= 3) {
            if (strlen($frequencyString) > 3) {
                if (!strpos($frequencyString, '.')) {
                    $frequency = floatval(substr($frequencyString, 0, 3).'.'.substr($frequencyString, 3));
                } else {
                    $frequency = floatval($frequencyString);
                }
            } else {
                $frequency = floatval($frequencyString);
            }
        }
        return $frequency;
	}

}
