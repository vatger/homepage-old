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
