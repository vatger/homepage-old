<?php

namespace App\Http\Controllers\Membership;

use Illuminate\Http\Request;
use App\Models\Membership\Account;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MembershipController extends Controller
{

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Membership Dashboard
	 *
	 * @return View
	 */
	public function index()
	{
		return $this->viewMake('membership.dashboard.index');
	}

	/**
	 * Display registration / initial login page
	 *
	 * @return View
	 */
	public function setup()
	{
        $account = Auth::user();
        if($account->setup_completed){
            return redirect()->route('membership.home');
        }
		return $this->viewMake('membership.dashboard.setup');
	}

	/**
	 * This is displayed to banned users
	 *
	 * @return [type] [description]
	 */
	public function banned()
	{
        $account = Auth::user();
        if(! $account->isCurrentlyBanned){
            return redirect()->route('membership.home');
        }
		return $this->viewMake('membership.dashboard.banned');
	}


    /**
	 * This is displayed to inactive users
	 *
	 * @return [type] [description]
	 */
	public function inactive()
	{
        $account = Auth::user();
        if(!$account->isInactive){
            return redirect()->route('membership.home');
        }
		return $this->viewMake('membership.dashboard.inactive');
	}

	/**
	 * API called endpoint to update Account information
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request)
	{
		$account = Auth::user();
        $account->loadMissing('setting');

		$validated = $request->validate(
			[
				'gdpr' => 'required|boolean',
				'settings' => 'boolean',
				'backupPassword' => ['confirmed', new \App\Rules\StrongPasswordRule],
				// 'backupPassword_confirmation' => 'required_with:backupPassword',
				'language' => 'required'
			]
		);

		// If the request is not made from the settings component
		if(!isset($validated['settings']) && !$account->setup_completed) {
			$account->setup_completed = $validated['gdpr'];
		}

		// Update the backup password if set
		if(!empty($validated['backupPassword'])){
			$account->password = Hash::make($validated['backupPassword']);
			$account->notify(new \App\Notifications\Membership\PasswordUpdatedNotification());
		}

        // Set the prefered language
        $account->setting->language = $validated['language'];
        $account->setting->save();

        $account->save();


        return $account->setup_completed;

	}

	/**
	 * Get the notifications for the authenticated user
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function notifications(Request $request) {
		if($request->ajax()) {
			return Auth::user()->notifications;
		} else {
			abort(403);
		}
	}

	/**
	 * Marks a given notification as read
	 * @param  Request                                        $request      [description]
	 * @return [type]                                                       [description]
	 */
	public function markNotificationAsRead(Request $request)
	{
		if($request->ajax()) {

			$validated = $request->validate([
				'notification' => 'required|exists:notifications,id',
			]);

			$account = Auth::user();
			$notification = \Illuminate\Notifications\DatabaseNotification::find($validated['notification']);
			if(!is_null($notification) && $account->id === $notification->notifiable->id) {
				$notification->markAsRead();
				return $notification;
			} else {
				return false;
			}
		} else {
			abort(403);
		}
	}

	/**
	 * Does the account has a forum account
	 *
	 * @param  Request $request [description]
	 * @return boolean          [description]
	 */
	public function hasForumAccount(Request $request)
	{
		if($request->ajax()) {
			$this->account->loadMissing('setting');

			return $this->account->setting->forum_id != null ? true : false;
		}
	}

	/**
	 * Get the forum username for an account.
	 *
	 * @param Request $request
	 *
	 * @return String The username
	 */
	public function getForumAccountName(Request $request)
	{
		if($request->ajax()) {
			$this->account->loadMissing('setting');

			return \App\Libraries\XenBridge::getForumUsername($this->account);
		}
	}

	/**
	 * Create a new forum access for a user
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function createForumAccount(Request $request)
	{
		if($request->ajax()) {
			$validated = $request->validate([
				'password' => 'required|confirmed',
			]);

			if($this->account->setting->forum_id == null) {
				$created = \App\Libraries\XenBridge::createForumAccount($this->account, $validated['password'], 0);
				if($created) {
					activity()
						->causedBy($this->account)
						->performedOn($this->account)
						->log('Forenaccount wurde erstellt!');
				}
			}
		}
		return false;
	}

	/**
	 * Get all active TeamSpeak Identities the account has
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function getTeamspeakIdentities(Request $request)
	{
		if($request->ajax()) {
			$this->account->loadMissing('teamspeakRegistrations');

			return $this->account->teamspeakRegistrations;
		}
		abort(403);
	}

	/**
	 * Start the automatic teamspeak registration process
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function createTeamspeakIdentity(Request $request)
	{
		/*if(!$request->ajax()) abort(403);

		// Check if the 10 identities limit has already been reached
		$this->account->loadMissing('teamspeakRegistrations');
		if(count($this->account->teamspeakRegistrations) >= 10) {
			return response()->json([
				'success' => false,
			]);
		}

		// Go ahead and try to create a new registration
		$registration = null;
		if($this->account->new_teamspeak_registration) {
			$registration = $this->account->new_teamspeak_registration->load('confirmation');
        } else {
            $ipAddress = $request->ip();
            $registration = $this->createTeamSpeakRegistration($this->account, $ipAddress);
		}
		// Is the registration confirmed?
        $confirmation = null;
        if (!$registration->confirmation) {
            $confirmation = $this->createTeamSpeakConfirmation(
                $registration->id,
                md5($registration->created_at->timestamp),
                $this->account->id
            );
        } else {
            $confirmation = $registration->confirmation;
        }

		$tsURL = 'ts3server://' . config('teamspeak.host') . '?nickname=' . $this->account->firstname . '%20'.$this->account->lastname . '&token=' . $confirmation->privilege_key;

		activity()
			->causedBy($this->account)
			->performedOn($this->account)
			->log('Automatische TS Registrierung abgeschlossen!');

        return response()->json(
            [
                'success' => true,
                'tslink' => $tsURL,
            ]
        );*/
	}

	/**
	 * Manually create / set a ts id for an account
	 *
	 * @param Request $request [description]
	 */
	public function setTeamspeakIdentity(Request $request)
	{
		if(!$request->ajax()) abort(403);

		$validated = $request->validate([
			'tsId' => 'required|string',
		]);


        $this->account->loadMissing('teamspeakRegistrations');
        if(count($this->account->teamspeakRegistrations) >= 10) {
            return response()->json([
                'success' => false,
                'message' => 'Too many TSIDs.',
            ]);
        }

        if(\App\Models\TeamSpeak\Registration::where('uid', $validated['tsId'])->exists()){
            return response()->json([ 'success' => false, 'message' => 'UID already assined.',]);
        }

        $result =\App\Libraries\TeamSpeakWebquery::registerViaUid($this->account, $request->ip(), $validated['tsId']);

        if ($result == false) return response()->json([ 'success' => false, 'message' => 'Unable to register.',]);

		$this->account->notify(new \App\Notifications\Membership\TeamSpeakIdentityCreated($validated['tsId']));

		activity()
			->causedBy($this->account)
			->performedOn($this->account)
			->log('TS Identity eingetragen: ' . $validated['tsId'] . '!');

        return response()->json(
            [
                'success' => true,
                'message' => $validated['tsId'].' successfully associated with your account.'
            ]
        );
	}

	/**
     * Create a new TeamSpeak Registration.
     *
     * @param Account $account    [description]
     * @param [type]  $ip         [description]
     * @param bool    $shouldSave [description]
     *
     * @return [type] [description]
     */
    protected function createTeamSpeakRegistration(Account $account, $ip, $shouldSave = true)
    {
        /*if ($account) {
            $reg = new \App\Models\TeamSpeak\Registration();
            $reg->account_id = $account->id;
            $reg->registration_ip = $ip;
            if ($shouldSave) {
                $reg->save();
            }

            return $reg;

            $this->account->notify(new \App\Notifications\Membership\TeamSpeakRegistrationCreated($ip));
        }

        return false;*/
    }

    /**
     * Create a new confirmation for a new registration.
     *
     * @param [type] $regId     [description]
     * @param [type] $conString [description]
     * @param [type] $accId     [description]
     *
     * @return [type] [description]
     */
    protected function createTeamSpeakConfirmation($regId, $conString, $accId)
    {
        /*$key_desc = 'CID:'.$accId.' RegID:'.$regId;
        $key_custom = 'ident=registration_id value='.$regId;
        $conf = new \App\Models\TeamSpeak\Confirmation();
        $conf->registration_id = $regId;
        $_ts = new \App\Libraries\TeamSpeak('VATGER Registration');
        $conf->privilege_key = $_ts->getInstance()
            ->serverGroupGetByName(config('teamspeak.default_group'))
            ->privilegeKeyCreate($key_desc, $key_custom);
        $conf->save();

        return $conf;*/
    }

    /**
     * Delete a teamspeak registration and also it's confirmation
     *
     * @param  Request $request [description]
     * @param  [type]  $tsreg   [description]
     * @return [type]           [description]
     */
    public function removeTeamspeakIdentity(Request $request, $tsreg)
    {
    	if(!$request->ajax()) abort(403);

    	$registration = \App\Models\TeamSpeak\Registration::findOrFail($tsreg);
    	$uid = $registration->uid;

        $removed = \App\Libraries\TeamSpeakWebquery::removeTSRegistation($registration);

        if($removed) {
			$this->account->notify(new \App\Notifications\Membership\TeamSpeakIdentityDeleted($uid));

			activity()
				->causedBy($this->account)
				->performedOn($this->account)
				->log('TS Identity ausgetragen! UID: ' . $uid . '!');

            return response()->json(true);
        }

    	return response()->json(false);
    }


    public function getProfileData(Request $request)
    {
        if(!$request->ajax()) abort(403);
        $data = [
            'cid' => $this->account->id,
            'hp_email' => $this->account->email,
            'forum_email' => \App\Libraries\XenBridge::getForumEmail($this->account)
        ];

        return response()->json($data);
    }

}
