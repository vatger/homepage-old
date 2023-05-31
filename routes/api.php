<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')
	->group(
		function () {
			Route::prefix('administration')
				->group(
					function () {

						// ATD Administration
						Route::prefix('atd')
							->group(
								function () {
									Route::post('euroscope/scenario', 'Administration\ATD\EuroScopeController@createScenario');
									// Handle Solo Endorsements
                                    Route::put('solos/{solo}/station', 'Administration\ATD\ATDController@switchStation');
                                    Route::put('solos/{solo}/forward', 'Administration\ATD\ATDController@forwardPhase');
                                    Route::put('solos/{solo}/switch', 'Administration\ATD\ATDController@switchPhase');
                                    Route::put('solos/{solo}', 'Administration\ATD\ATDController@extendSolo');
                                    Route::delete('solos', 'Administration\ATD\ATDController@deleteSolo');
                                    Route::put('solos', 'Administration\ATD\ATDController@approveSolo');
                                    Route::post('solos', 'Administration\ATD\ATDController@createSolo');
									Route::get('solos', 'Administration\ATD\ATDController@solos');

									Route::get('', 'Administration\ATD\ATDController@index');
								}
							);
                        // Eventroutes Administration
                        Route::prefix('event/routes')
                            ->group(
                                function () {
                                    Route::get('/', 'Administration\Events\EventRoutesController@listRoutes');
                                    Route::get('{er}', 'Administration\Events\EventRoutesController@routeDetails');
                                    Route::post('/', 'Administration\Events\EventRoutesController@createRoute');
                                    Route::patch('{er}', 'Administration\Events\EventRoutesController@editRoute');
                                    Route::delete('{er}','Administration\Events\EventRoutesController@deleteRoute');;
                                    Route::post('{er}','Administration\Events\EventRoutesController@createLeg');
                                    Route::delete('{er}/{leg}','Administration\Events\EventRoutesController@deleteLeg');
                                    Route::patch('{er}/{leg}','Administration\Events\EventRoutesController@manualCompleteLeg');
                                    Route::get('flightdata/{flightdata}','Administration\Events\EventRoutesController@showFlightData');
                                }
                            );

                        // gitlab
                        Route::prefix('gitlab')
                            ->group(
                                function() {
                                    Route::get('/', 'Administration\Services\GitlabController@getSettings');
                                    Route::post('/', 'Administration\Services\GitlabController@createAccount');

                        });

						Route::get('download', 'Administration\Filebase\DownloadController@download');

						Route::get('myimages', 'Administration\Filebase\ImageController@myImages');
						Route::prefix('images')
							->group(function () {
								Route::delete('{image}', 'Administration\Filebase\ImageController@deleteImage');
								Route::put('{image}/approve', 'Administration\Filebase\ImageController@approveImage');
								Route::put('{image}/deny', 'Administration\Filebase\ImageController@denyImage');
								Route::get('{image}', 'Administration\Filebase\ImageController@getImage');
								Route::post('upload', 'Administration\Filebase\ImageController@uploadImage');
								Route::get('', 'Administration\Filebase\ImageController@images');
							});

						// Navigation Administration
						Route::prefix('navigation')
							->group(
								function () {
									Route::get('aip/{mode}', 'Administration}Navigation\SectorfileController@buildFromAIP');
									Route::post('sct/glg', 'Administration\Navigation\GroundlayoutController@render');
                                    // Route::get('sct/glg/sample', 'Administration\GroundlayoutController@downloadSample');
                                    Route::get('sct/glg', 'Administration\Navigation\GroundlayoutController@download');
									Route::post('sct/combine', 'Administration\Navigation\SectorfileController@combineSectorFiles');
									Route::delete('chart/{chart}', 'Administration\Navigation\ChartController@remove');
									Route::post('chart', 'Administration\Navigation\ChartController@create');
									Route::put('chart/{chart}', 'Administration\Navigation\ChartController@update');
									Route::get('chart', 'Administration\Navigation\ChartController@index');
									// Create / Update / Delete ATS Stations
									Route::delete('station/{station}', 'Administration\Navigation\StationController@deleteStation');
									Route::put('station/{station}', 'Administration\Navigation\StationController@updateStation');
									Route::post('station', 'Administration\Navigation\StationController@createStation');
									Route::get('station', 'Administration\Navigation\StationController@index');
									// CRUD for navaids
									Route::delete('navaids/{navaid}', 'Administration\Navigation\NavaidController@delete');
									Route::put('navaids/{navaid}', 'Administration\Navigation\NavaidController@update');
									Route::post('navaids', 'Administration\Navigation\NavaidController@create');
									Route::get('navaids', 'Administration\Navigation\NavaidController@index');
									// Manipulate Aerodrome Stuff
									Route::delete('aerodrome/{aerodrome}/navaids/{navaid}', 'Administration\Navigation\AerodromeController@removeNavaid');
									Route::put('aerodrome/{aerodrome}/navaids', 'Administration\Navigation\AerodromeController@addNavaid');
									Route::delete('aerodrome/{aerodrome}/runways/{runway}', 'Administration\Navigation\AerodromeController@removeRunway');
									Route::put('aerodrome/{aerodrome}/runways/{runway}', 'Administration\Navigation\AerodromeController@editRunway');
									Route::post('aerodrome/{aerodrome}/runways', 'Administration\Navigation\AerodromeController@addRunway');
									Route::delete('aerodrome/{aerodrome}/charts/{chart}', 'Administration\Navigation\AerodromeController@removeChart');
									Route::put('aerodrome/{aerodrome}/charts', 'Administration\Navigation\AerodromeController@addChart');
									Route::delete('aerodrome/{aerodrome}/stations/{station}', 'Administration\Navigation\AerodromeController@removeStation');
									Route::post('aerodrome/{aerodrome}/stations', 'Administration\Navigation\AerodromeController@addStation');
									Route::put('aerodrome/{aerodrome}/stations', 'Administration\Navigation\AerodromeController@updateStationOrder');
									Route::post('aerodrome/{aerodrome}/stands', 'Administration\Navigation\AerodromeController@updateStands');
									Route::get('aerodrome/{aerodrome}/stands', 'Administration\Navigation\AerodromeController@getStands');
									Route::put('aerodrome/{aerodrome}', 'Administration\Navigation\AerodromeController@updateGeneral');
									Route::get('aerodrome/{icao}', 'Administration\Navigation\AerodromeController@show');
									Route::post('aerodrome', 'Administration\Navigation\AerodromeController@createAerodrome');
									Route::get('regionalgroups', 'Administration\Navigation\AerodromeController@getAvailableRegionalgroups');
								}
							);

						// Regionalgroup Administration
						Route::prefix('regionalgroups')
							->group(
								function () {

									Route::delete('{regionalgroup}/mentors/{mentor}', 'Administration\Regionalgroup\StaffController@removeMentor');
									Route::post('{regionalgroup}/mentors', 'Administration\Regionalgroup\StaffController@assignMentor');

									Route::delete('{regionalgroup}/navigators/{navigator}', 'Administration\Regionalgroup\StaffController@removeNavigator');
									Route::post('{regionalgroup}/navigators', 'Administration\Regionalgroup\StaffController@assignNavigator');

									Route::delete('{regionalgroup}/eventler/{eventler}', 'Administration\Regionalgroup\StaffController@removeEventler');
									Route::post('{regionalgroup}/eventler', 'Administration\Regionalgroup\StaffController@assignEventler');

									Route::post('{regionalgroup}/chief', 'Administration\Regionalgroup\StaffController@assignChief');
									Route::post('{regionalgroup}/deputy', 'Administration\Regionalgroup\StaffController@assignDeputy');

									Route::put('{regionalgroup}/requests/{regionalgroupRequest}/accept', 'Administration\Regionalgroup\RequestController@acceptRequest');
									Route::put('{regionalgroup}/requests/{regionalgroupRequest}/deny', 'Administration\Regionalgroup\RequestController@denyRequest');

									Route::delete('{regionalgroup}/accounts/{account}', 'Administration\Regionalgroup\RegionalgroupController@removeAccount');

                                    Route::post('{regionalgroup}/template', 'Administration\Regionalgroup\TemplateController@create');
                                    Route::put('{regionalgroup}/template/{template}', 'Administration\Regionalgroup\TemplateController@edit');
                                    Route::delete('{regionalgroup}/template/{template}', 'Administration\Regionalgroup\TemplateController@delete');

									Route::put('{regionalgroup}', 'Administration\Regionalgroup\RegionalgroupController@update');
									Route::get('{regionalgroup}', 'Administration\Regionalgroup\RegionalgroupController@show');
									Route::get('', 'Administration\Regionalgroup\RegionalgroupController@index');
								}
							);

						Route::prefix('forum')
							->group(
								function () {
									Route::delete('group/{fg}', 'Administration\Forum\ForumgroupController@remove');
									Route::post('groups', 'Administration\Forum\ForumgroupController@create');
									Route::get('groups', 'Administration\Forum\ForumgroupController@index');
								}
							);

						// Membership Permission Administration
						Route::get('permission', 'Administration\Membership\PermissionController@index');

						// Membership Group Administration
						Route::post('group/{group}/forumgroups', 'Administration\Membership\GroupController@assignForumGroup');
						Route::delete('group/{group}/forumgroups', 'Administration\Membership\GroupController@unassignForumGroup');
						Route::post('group/{group}/permission', 'Administration\Membership\GroupController@addPermission');
						Route::delete('group/{group}/permission', 'Administration\Membership\GroupController@removePermission');
						Route::post('group/{group}/account', 'Administration\Membership\GroupController@addAccount');
						Route::delete('group/{group}/account', 'Administration\Membership\GroupController@removeAccount');
						Route::get('group/{group}', 'Administration\Membership\GroupController@show');
						Route::get('group', 'Administration\Membership\GroupController@index');

						// Membership Account Administration
						Route::delete('membership/{account}/note/{note}', 'Administration\Membership\AccountController@removeNote');
						Route::post('membership/{account}/note', 'Administration\Membership\AccountController@addNote');
						Route::delete('membership/{account}/ban/{ban}', 'Administration\Membership\AccountController@removeBan');
						Route::post('membership/{account}/ban', 'Administration\Membership\AccountController@addBan');
						Route::get('membership/{account}', 'Administration\Membership\AccountController@show');
						Route::get('membership', 'Administration\Membership\AccountController@index');

						Route::get('', 'Administration\Dashboard\DashboardController@getStatistics');
					}
				);
			Route::prefix('atd')
				->group(
					function () {

						Route::get('trainings/status', 'ATD\TrainingController@status');
						Route::delete('trainings/{training}/{trainingsession}', 'ATD\TrainingController@removeSession');
						Route::post('trainings/{training}', 'ATD\TrainingController@createSession');
						Route::post('trainings', 'ATD\TrainingController@create');
						Route::get('trainings', 'ATD\TrainingController@index');
						Route::get('regionalgroups', 'ATD\TrainingController@getAvailableRegionalgroups');
					}
				);
			Route::prefix('regionalgroup')
				->group(
					function () {
						Route::delete('requests/{regionalgroupRequest}', 'Regionalgroup\RequestController@delete');
						Route::post('requests', 'Regionalgroup\RequestController@create');
						Route::get('requests', 'Regionalgroup\RequestController@index');
						Route::get('news/{regionalgroup}', 'Regionalgroup\RegionalgroupController@news');
						Route::get('', 'Regionalgroup\RegionalgroupController@index');
					}
				);
			Route::prefix('membership')
				->group(
					function () {
						Route::put('notifications', 'Membership\MembershipController@markNotificationAsRead');
						Route::get('notifications', 'Membership\MembershipController@notifications');
						// Membership Settings
						// Forum Access
						Route::post('forum', 'Membership\MembershipController@createForumAccount');
						Route::get('forum/username', 'Membership\MembershipController@getForumAccountName');
						Route::get('forum', 'Membership\MembershipController@hasForumAccount');

						// Update / Set / Create / Delete Teamspeak Identities
						Route::delete('teamspeak/{tsreg}', 'Membership\MembershipController@removeTeamspeakIdentity');
						Route::put('teamspeak', 'Membership\MembershipController@setTeamspeakIdentity');
						//Route::post('teamspeak', 'Membership\MembershipController@createTeamspeakIdentity');
						Route::get('teamspeak', 'Membership\MembershipController@getTeamspeakIdentities');

						// Update general account details
						Route::put('account', 'Membership\MembershipController@update');
                        Route::get('my-profile', 'Membership\MembershipController@getProfileData');
                        Route::get('my-keys', 'Membership\SurveyKeysController@myKeys');
					}
				);
			Route::prefix('booking')
				->group(
					function () {
						Route::get('atc/personal', 'Bookings\AtcBookingController@personal');
						Route::delete('atc/{booking}', 'Bookings\AtcBookingController@delete');
						Route::put('atc/{booking}', 'Bookings\AtcBookingController@edit');
						Route::post('atc', 'Bookings\AtcBookingController@book');
					}
				);
            // Eventroutes api endpoints
            Route::prefix('event/routes')
                ->group(
                    function () {
                        Route::get('/', 'Events\EventRouteController@getEventRoutes');
                        Route::get('{route}', 'Events\EventRouteController@getRouteLegs');
                        Route::get('{route}/signup','Events\EventRouteController@signupEventRoute');
                        Route::get('{route}/signout','Events\EventRouteController@signoutEventRoute');
                    }
                );
		}
    );



// Routes that will try to authenticate users, if that does not work user will be unauthenticated
Route::middleware('optionalAuth:api')
    ->group(
        function () {
            Route::prefix('navigation')
                ->group(
                    function () {
                        Route::get('chart/{chart}', 'Authorization\AuthorizationController@authorizeChartAccess');
                    }
				);
			Route::prefix('network')
				->group(
					function () {
						Route::get('atc/local/{dashboard?}', 'Network\DatafeedApiController@getLocalAtc');
					}
                );
            // ATD Solo endorsements
            Route::prefix('atd')
                ->group(
                    function () {
                        Route::get('solos', 'ATD\ATDController@solos');
                    }
                );
        }
    );



// Booking api endpoints
Route::prefix('booking')
	->group(
		function () {
			Route::get('atc/daterange/{start}/{end?}', 'Bookings\AtcBookingController@dateRange');
			Route::get('atc', 'Bookings\AtcBookingController@index');
		}
	);

// Navigation Endpoints
Route::prefix('navigation')
	->group(
		function () {
			Route::get('stations/{bookable?}', 'Navigation\StationController@getBookableStations');
			Route::get('aerodrome/{icao}/stands', 'Navigation\AerodromeController@getStandStatus');
			Route::get('aerodrome/{icao}/atc', 'Navigation\AerodromeController@getControllerActivity');
			Route::get('aerodrome/{icao}/flights', 'Navigation\AerodromeController@getPilotActivity');
			Route::get('aerodrome/{icao}', 'Navigation\AerodromeController@getAerodrome');
			Route::get('aerodromes/{local?}', 'Navigation\AerodromeController@getAerodromes');
			Route::get('airports/{nonjson?}', 'Navigation\AerodromeController@getAirports');
            Route::get('boundaries/{callsign}', 'Navigation\SectordataController@getSector');

		}
	);

// Forum
Route::prefix('forum')
	->group(
		function () {
			Route::get('newsfeed', 'Forum\ForumController@getNews');
		}
	);



// Network Data Feed Api Endpoints
Route::prefix('network')
	->group(
		function () {
			//Route::get('atc/local/{dashboard?}', 'Network\DatafeedApiController@getLocalAtc');
			Route::get('atc', 'Network\DatafeedApiController@getOnlineAtc');
			Route::get('pilots', 'Network\DatafeedApiController@getActiveFlights');
			Route::get('connected', 'Network\DatafeedApiController@getConnectedClients');
			Route::get('weather/{icao}', 'Network\DatafeedApiController@getWeather');
		}
	);

/**
 * Wiki Api Data Endpoints
 */
Route::prefix('wiki')
	->group(
		function () {
			Route::get('aerodrome/{icao}/stations', 'Wiki\ApiController@stations');
			Route::get('aerodrome/{icao}/runways', 'Wiki\ApiController@runways');
			Route::get('aerodrome/{icao}', 'Wiki\ApiController@aerodrome');
		}
	);

// API ENDPOINTS
Route::get('teamspeak/{account_id}', 'APIController@dbids');

// Static Api Endpoints
Route::prefix('static')
	->group(
		function () {
			Route::get('legal/gdpr', function (Request $request) {
				if(!$request->ajax())
				{
					return '';
				} else {
					return \Illuminate\Support\Facades\Storage::get('static/gdpr.html');
				}
			});
		}
	);


Route::get('account/{account_id}/isger', 'APIController@isger');
