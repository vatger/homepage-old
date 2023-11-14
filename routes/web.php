<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Statisticcenter subdomain
 *
Route::domain('stats.'.parse_url(config('app.url'), PHP_URL_HOST))
	->group(
		function () {
			Route::prefix('flight')
				->group(
					function () {
						Route::post('', 'Statistics\FlightController@search')->name('statistics.flight.search');
						Route::get('', 'Statistics\FlightController@index')->name('statistics.flight.home');
					}
				);
			Route::prefix('aerodrome')
				->group(
					function () {
						Route::get('{aerodromeByIcao}/{from}/{till}', 'Statistics\AerodromeController@show')->name('statistics.aerodrome.icao');
						Route::get('', 'Statistics\AerodromeController@index')->name('statistics.aerodrome.home');
					}
				);
			Route::prefix('atc')
				->group(
					function () {
						Route::get('{stationIdent}', 'Statistics\AtcController@show')->name('statistics.atc.show');
						Route::post('', 'Statistics\AtcController@search')->name('statistics.atc.search');
						Route::get('', 'Statistics\AtcController@index')->name('statistics.atc.home');
					}
				);
			Route::get('', 'Statistics\StatisticcenterController@index')->name('statistics.home');
		}
	);
*/

//Route::domain(parse_url(config('app.url'), PHP_URL_HOST))
//	->group(
Route::middleware([])->group(		function () {
			/**
			 * Routes the user must be authenticated for,
			 * not have been banned and also must have made the basic setup
			 */
			Route::middleware(['auth', 'setupCompleted', 'checkBanned', 'checkInactive'])
				->group(
					function () {
						Route::prefix('administration')
							->group(
								function () {
									Route::get('{vue_capture?}', 'Administration\Dashboard\DashboardController@index')
										->where('vue_capture', '[\/\w\.-]*')
										->name('administration.home');
								}
							);
						Route::prefix('membership')
							->group(
								function () {

//									Route::get('setup', 'Membership\MembershipController@setup')
//										->withoutMiddleware('setupCompleted')
//										->name('membership.setup');
//									Route::get('banned', 'Membership\MembershipController@banned')
//										->withoutMiddleware(['setupCompleted', 'checkBanned', 'checkInactive'])
//										->name('membership.banned');
//                                    Route::get('inactive', 'Membership\MembershipController@inactive')
//										->withoutMiddleware(['setupCompleted', 'checkBanned', 'checkInactive'])
//										->name('membership.inactive');

									Route::get('{vue_capture?}', 'Membership\MembershipController@index')
										->where('vue_capture', '[\/\w\.-]*')
										->name('membership.home');
								}
							);
					}
				);

//			Route::prefix('pilots')
//				->group(
//					function ()
//					{
//						Route::get('{vue_capture?}', 'Pilots\PilotController@index')
//							->where('vue_capture', '[\/\w\.-]*')
//							->name('pilot.home');
//					}
//				);

//			Route::prefix('controllers')
//				->group(
//					function ()
//					{
//						Route::get('{vue_capture?}', 'Controllers\ControllerController@index')
//							->where('vue_capture', '[\/\w\.-]*')
//							->name('controller.home');
//					}
//				);

			/**
			 * Routes to access publicly available resources
			 */
//			Route::prefix('resources')
//				->group(
//					function ()
//					{
//						Route::get('image/{image}', 'Resources\ResourceController@image');
//					}
//				);

			/**
			 * Impersonate other users
			 */
//			Route::impersonate();

			/**
			 * Authentication Routes
			 */
			Route::prefix('vatauth')
				->group(
					function () {
//							Route::post('local', 'Authentication\VatsimConnectController@local')->name('vatauth.local');
							Route::get('failed', 'Authentication\VatsimConnectController@failed')->name('vatauth.failed');
							Route::get('login', 'Authentication\VatsimConnectController@login')->name('vatauth.login');
							Route::get('logout', 'Authentication\VatsimConnectController@logout')->name('vatauth.logout');
						}
				);

			/**
			 * Frontend Routes
			 */

//            Route::get('partners', 'Landingpage\LandingpageController@partners')->name('partners');


			// GDPR and Impressum

			/*
			 * Translation file combiner to support translations for the frontend
			 */
			Route::get('/js/{language}/lang.js', 'Translation\TranslationController@getLanguage')->name('assets.lang');
			// Change the language on the fly
			Route::get('lang/{language?}', function ($language = 'de') {
				Session::put('language', $language);
				return redirect()->back()->withInput();
			})->name('language.change');
			// Fallback / Landingpage
			Route::get('/', 'Landingpage\LandingpageController@index')->name('home');

		}
	);
