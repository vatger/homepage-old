<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Http\Request;
use League\OAuth2\Client\Token;
use App\Models\Membership\Account;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Providers\Vatsim\VatauthProvider;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\IdentityProviderException;
use UnexpectedValueException;

class VatsimConnectController extends Controller
{

	use AuthenticatesUsers;

	protected $_provider;

	function __construct()
	{
		parent::__construct();
		$this->_provider = new VatauthProvider();
	}

	public function login(Request $request)
	{
		if(!$request->has('code') || !$request->has('state'))
		{
			try{
				$response = \Illuminate\Support\Facades\Http::timeout(3)->get(config('vatsim_auth.base'));
				if($response->successful()) {
					// Unkown authentication process state.
					// Begin at step 1
					$authUrl = $this->_provider->getAuthorizationUrl();
					$request->session()->put('vatsimauthstate', $this->_provider->getState());
					return redirect()->away($authUrl);
				} else {
					// Vatsimauth not available. Fallback to local sign in process.
					return redirect()->route('vatauth.failed');
				}
			} catch(\Illuminate\Http\Client\ConnectionException $ce) {
				// Vatsimauth not available. Fallback to local sign in process.
				return redirect()->route('vatauth.failed');
			}
		} elseif ($request->input('state') !== session()->pull('vatsimauthstate')) {
			// Wrong state detected. Fallback to failed state
			return redirect()->route('vatauth.failed');
		} else {
			return $this->_verifyLogin($request); // Do the login!!!
		}
	}

	protected function _verifyLogin(Request $request)
	{
		try {
			$accessToken = $this->_provider->getAccessToken('authorization_code', [
				'code' => $request->input('code')
			]);
		} catch (IdentityProviderException $e) {
			return redirect()->route('vatauth.failed');
		}

		/**
		 * Get the resource owner
		 * @var ResourceOwner Object
		 */
		try{
			$resourceOwner = json_decode(json_encode($this->_provider->getResourceOwner($accessToken)->toArray()));
		} catch(UnexpectedValueException $e){
			return redirect()->route('vatauth.failed');
		}
		
		if(	!isset($resourceOwner->data) ||
			!isset($resourceOwner->data->cid) ||
			!isset($resourceOwner->data->personal->name_first) ||
			!isset($resourceOwner->data->personal->name_last) ||
			!isset($resourceOwner->data->personal->email) ||
			$resourceOwner->data->oauth->token_valid !== "true"
		) {
			return redirect()->route('vatauth.failed');
        }
        // Check for duplicate email
        if(Account::where('email',  $resourceOwner->data->personal->email)->where('id', '!=', $resourceOwner->data->cid)->exists())
        {
            return $this->viewMake('frontend.authentication.dupemail');
        }

        activity()->disableLogging(); // Disable logging here to only log data updated from the automated system.

		$account = $this->_completeLogin($resourceOwner, $accessToken);

		auth()->login($account, true);

		activity()->enableLogging();

		return redirect()->intended(route('membership.home'));
	}



	protected function _completeLogin($resourceOwner, $accessToken)
	{
		// Find the Account.
		$account = Account::find($resourceOwner->data->cid);
        //If not we need to create a new one
		if(!$account) {
            // Create new one
			$account = new Account;
			$account->id = $resourceOwner->data->cid;
			// Now we need to set a token for local api calls
			$account->api_token = \Illuminate\Support\Str::random(80);
		} else {
			// Refresh the API token
			$account->renewApiToken();
		}
		$account->firstname = $resourceOwner->data->personal->name_first;
		$account->lastname = $resourceOwner->data->personal->name_last;

		// $account->firstname = mb_convert_encoding($resourceOwner->data->personal->name_first, "Windows-1252", "UTF-8");
		// $account->lastname = mb_convert_encoding($resourceOwner->data->personal->name_last, "Windows-1252", "UTF-8");

		$account->email = $resourceOwner->data->personal->email;
		$account->save();

		//
		// If the user has given us permanent access to the data
		if($resourceOwner->data->oauth->token_valid) {
			$account->access_token = $accessToken->getToken();
			$account->refresh_token = $accessToken->getRefreshToken();
			$account->token_expires = $accessToken->getExpires();
		}

		$account->save();

		$account->loadMissing('setting');
        if (null == $account->setting) {
            /*
             * This account does not have a setting association.
             * It is a new account and we need to create that now
             */
            $account->setting()->save(
                new \App\Models\Membership\Account\Setting(
                    ['language' => Session::get('language')]
                )
            );
        } else {
            // Set the session language according to users setting
            Session::put('language', $account->setting->language);
        }

        $account->loadMissing('data');
        if (null == $account->data) {
            $account->data()->save(new \App\Models\Membership\Account\Data(['account_id' => $account->id]));
            $account->load('data');
        }
        
        $account->data->active = true;
        $account->data->suspended = false;

        /**
         * If the scope vatsim_details was granted
         */
        if(isset($resourceOwner->data->vatsim) && isset($resourceOwner->data->personal->country)) {
            $account->data->rating_atc = $resourceOwner->data->vatsim->rating->id;
            $account->data->rating_pilot = $resourceOwner->data->vatsim->pilotrating->id;

            $account->data->active = $account->data->rating_atc > 0;
            $account->data->suspended = $account->data->rating_atc < 0;

            /*
             * Update accounts region/division associations
             */
            // User country
            $account->data->country_code = $resourceOwner->data->personal->country->id;
            $account->data->country_name = $resourceOwner->data->personal->country->name;
            // User Region
            $account->data->region_code = $resourceOwner->data->vatsim->region->id;
            $account->data->region_name = $resourceOwner->data->vatsim->region->name;
            // User Division
            $account->data->division_code = $resourceOwner->data->vatsim->division->id;
            $account->data->division_name = $resourceOwner->data->vatsim->division->name;
            // User Subdivision
            $account->data->subdivision_code = $resourceOwner->data->vatsim->subdivision->id;
            $account->data->subdivision_name = $resourceOwner->data->vatsim->subdivision->name;
        }

        $account->data->save();

		return $account;
	}

	public function logout()
	{
		auth()->logout();

		return redirect()->route('home');
	}

	public function failed()
	{
		return $this->viewMake('frontend.authentication.failed');
    }

	public function local(Request $request)
	{
		$validated = $request->validate(
			[
				'cid' => 'required|exists:membership_accounts,id',
				'lpwd' => 'required',
			]
		);

		if(Auth::attempt(['id' => $validated['cid'], 'password' => $validated['lpwd']])) {
			return redirect()->route('membership.home');
		} else {
			return redirect()->route('vatauth.failed');
		}
	}

}
