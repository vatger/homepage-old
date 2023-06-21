<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Bookings\VatBookController;
use App\Libraries\ATCBookingsApi;
use Illuminate\Http\Request;
use App\Models\Booking\AtcSessionBooking;
use Carbon\Carbon;

class AtcBookingController
{

	/**
	 * Gets all bookings within the next 48 hours
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function index(Request $request)
	{
		if($request->ajax()) {
            if(auth()->check() || $request->bearerToken() == config('booking.atcBookingToken'))
			    return AtcSessionBooking::orderBy('starts_at', 'ASC')
                    ->with('station', 'controller')
                    ->whereBetween('ends_at', [Carbon::now()->utc(), Carbon::now()->utc()->addHours(48)])
                    ->get();
            else
                return AtcSessionBooking::orderBy('starts_at', 'ASC')
                    ->with('station')
                    ->whereBetween('ends_at', [Carbon::now()->utc(), Carbon::now()->utc()->addHours(48)])
                    ->get();
		}
	}

	/**
	 * Gets bookings between a given start date and an optional end date
	 *
	 * The start date will allways be set to the date with 00:00:00 hours set.
	 * The end date will in any case be set to the date with 23:59:59 appended.
	 *
	 * @param  Request $request [description]
	 * @param  [type]  $start   [description]
	 * @param  [type]  $end     [description]
	 * @return [type]           [description]
	 */
	public function dateRange(Request $request, $start, $end = null)
	{
		if($request->ajax()) {
			try {
	            $s = Carbon::createFromFormat('d.m.Y', $start);
	            if ($end == null || $end == 'null') {
	                // Only a start date has been passed.
	                // So we will find any bookings that are made for that day

	                // build a fake enddate that will be the startdate plus 23:59:59
	                // so the end of that day
	                $e = Carbon::createFromFormat('d.m.Y', $start)
	                    ->addHours(23)
	                    ->addMinutes(59)
	                    ->addSeconds(59);
	            } else {
	                $e = Carbon::createFromFormat('d.m.Y', $end)
	                	->addHours(23)
	                    ->addMinutes(59)
	                    ->addSeconds(59);
	            }
	            // As we now have our end date
	            // we can try to find stuff
                if(auth()->check() || $request->bearerToken() == config('booking.atcBookingToken'))
                    return AtcSessionBooking::orderBy('starts_at', 'ASC')
                        ->with('station', 'controller')
                        ->whereBetween('starts_at', [$s, $e])
                        ->orWhereBetween('ends_at', [$s, $e])
                        ->get();
                else
                    return AtcSessionBooking::orderBy('starts_at', 'ASC')
                        ->with('station')
                        ->whereBetween('starts_at', [$s, $e])
                        ->orWhereBetween('ends_at', [$s, $e])
                        ->get();
	        } catch (InvalidArgumentException $e) {
	            return null;
	        }
		}
	}

	/**
	 * Gets all bookings of a given controller
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function personal(Request $request)
	{
		if($request->ajax()) {
			return AtcSessionBooking::forAccountId($this->account->id)
						->with('station')
						->where('starts_at', '>=', Carbon::now()->utc()->subDays(1))
						->orderBy('starts_at', 'ASC')
						->get();
		}
	}

	/**
	 * Evaluates and creates a new booking
	 *
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function book(Request $request)
	{
		if($request->ajax())
		{
			// Validate the requested data
			// If this fails a 422 response with validation errors will be send back
			$validated = $request->validate([
				'station' => 'required|exists:navigation_stations,id',
				'from' => 'required|date_format:"d.m.Y H:i"',
                'till' => 'required|date_format:"d.m.Y H:i"',
                'training' => 'boolean',
                'event' => 'boolean',
                'voice' => 'boolean',
			]);

			// Let's validate even further
			$this->account->loadMissing('data');

	        $now = \Carbon\CarbonImmutable::now('UTC');

	        $startsAt = Carbon::createFromFormat('d.m.Y H:i', $validated['from'], 'UTC');
	        $endsAt = Carbon::createFromFormat('d.m.Y H:i', $validated['till'], 'UTC');
	        // Perform important checks the default validation can not handle
	        // Check that the end is after the start date
			// and that the start is maximum 2 hours in the past

			// Accept booking only if the start is maximum 2 hours in the past.
			if($now->diffInHours($startsAt, false) < -2) {
				return response()->json(
					[
						'errors' => [
							trans('booking.errors.timeframePast'),
						],
					],
					422
				);
			}

			// Accept booking only if the begin is prior to the end
        	if ($endsAt <= $startsAt) {
	            return response()->json(
	                [
	                    'errors' => [
	                    	trans('booking.errors.timeframeSense'),
	                    ],
	                ],
	                422
	            );
			}

	        // Check for timeframe size
	        // Session must be at least 60 minutes long and must not exceed 24 hours
	        $bookingLength = $startsAt->diffInHours($endsAt);
	        if ($bookingLength < 1 || $bookingLength >= 24) {

	            return response()->json(
	                [
	                    'errors' => [
	                    	trans('booking.errors.timeframeLimits'),
	                    ],
	                ],
	                422
	            );
	        }
	        // The account must be eligable to controll on the network
	        if ($this->account->data->rating_atc <= 1) {
	            // event(new \App\Events\NotifyAccountEvent($this->account, 'error', 'ATC Booking', 'Failed to book because you are not eligable to book.'));

	            return response()->json(
	                [
	                    'errors' => [
	                    	trans('booking.errors.notEligable'),
	                    ],
	                ],
	                422
	            );
            }

            // there is already a booking on this station
            if(AtcSessionBooking::where('station_id', $validated['station'])
                ->where( function($query) use ($startsAt, $endsAt) {
                    $query->where(function($query) use ($startsAt) {
                        $query->where('starts_at','<', $startsAt)
                            ->where('ends_at', '>', $startsAt);
                    })->orWhere(function($query) use ($endsAt){
                        $query->where('starts_at','<', $endsAt)
                            ->where('ends_at', '>', $endsAt);
                    })->orWhere(function($query) use ($startsAt, $endsAt){
                        $query->where('starts_at','>=', $startsAt)
                            ->where('ends_at', '<=', $endsAt);
                    });
                })->count() > 0){

                return response()->json(
					[
						'errors' => [
							trans('booking.errors.alreadyBooked'),
						],
					],
					422);

            }

            // the booking is way to far in the future
            if ($now->diffInDays($startsAt, false) > 45){
                return response()->json(['errors' => [trans('booking.errors.toFarFuture'), ], ], 422);
            }

	        // All checks passed.
	        // Create and save the booking
	        $booking = new AtcSessionBooking();
	        $booking->vatbook_id = 0;
	        $booking->station_id = $validated['station'];
	        $booking->controller_id = $this->account->id;
	        $booking->training = $validated['training'] ? true : false;
	        $booking->voice = $validated['voice'] ? true : false;
	        $booking->event = $validated['event'] ? true : false;
	        $booking->starts_at = $startsAt;
	        $booking->ends_at = $endsAt;
	        //$booking->save();

            $res = ATCBookingsApi::createAndSaveBooking($booking);

            if($res['ok']) {
                $this->account->notify(new \App\Notifications\Booking\AtcBookingCreatedNotification());
                return response()->json(['success' => true], 200);
            }
            else {
                $this->account->notify(new \App\Notifications\Booking\AtcBookingCreatedNotification());
                return response()->json(['success' => true, 'errors' => [$res['message']],], 422);
            }
		}
	}

	/**
	 * Evaluates and saves a given, modified booking
	 *
	 * @param  Request           $request [description]
	 * @param  AtcSessionBooking $booking [description]
	 * @return [type]                     [description]
	 */
	public function edit(Request $request, AtcSessionBooking $booking)
	{
		if($request->ajax())
		{
			// Validate the requested data
			// If this fails a 422 response with validation errors will be send back
			$validated = $request->validate([
				'station' => 'required|exists:navigation_stations,id',
				'from' => 'required|date_format:"d.m.Y H:i"',
                'till' => 'required|date_format:"d.m.Y H:i"',
                'training' => 'boolean',
                'event' => 'boolean',
                'voice' => 'boolean',
			]);

			// Let's validate even further
			$this->account->loadMissing('data');

	        $now = \Carbon\CarbonImmutable::now('UTC');

	        $startsAt = Carbon::createFromFormat('d.m.Y H:i', $validated['from'], 'UTC');
	        $endsAt = Carbon::createFromFormat('d.m.Y H:i', $validated['till'], 'UTC');
	        // Perform important checks the default validation can not handle
	        // Check that the end is after the start date
			// and that the start is maximum 2 hours in the past

			// Accept booking only if the start is maximum 2 hours in the past.
			if($now->diffInHours($startsAt, false) < -2) {
				return response()->json(
					[
						'errors' => [
							trans('booking.errors.timeframePast'),
						],
					],
					422
				);
			}

			// Accept booking only if the begin is prior to the end
        	if ($endsAt <= $startsAt) {
	            return response()->json(
	                [
	                    'errors' => [
	                    	trans('booking.errors.timeframeSense'),
	                    ],
	                ],
	                422
	            );
			}

	        // Check for timeframe size
	        // Session must be at least 60 minutes long and must not exceed 24 hours
	        $bookingLength = $startsAt->diffInHours($endsAt);
	        if ($bookingLength < 1 || $bookingLength >= 24) {

	            return response()->json(
	                [
	                    'errors' => [
	                    	trans('booking.errors.timeframeLimits'),
	                    ],
	                ],
	                422
	            );
	        }
	        // The account must be eligable to controll on the network
	        if ($this->account->data->rating_atc <= 1) {
	            // event(new \App\Events\NotifyAccountEvent($this->account, 'error', 'ATC Booking', 'Failed to book because you are not eligable to book.'));

	            return response()->json(
	                [
	                    'errors' => [
	                    	trans('booking.errors.notEligable'),
	                    ],
	                ],
	                422
	            );
	        }

            // there is already a booking on this station
            if(AtcSessionBooking::where('station_id', $validated['station'])
                ->where('id', '!=', $booking->id)
                ->where( function($query) use ($startsAt, $endsAt) {
                    $query->where(function($query) use ($startsAt) {
                        $query->where('starts_at','<', $startsAt)
                            ->where('ends_at', '>', $startsAt);
                    })->orWhere(function($query) use ($endsAt){
                        $query->where('starts_at','<', $endsAt)
                            ->where('ends_at', '>', $endsAt);
                    })->orWhere(function($query) use ($startsAt, $endsAt){
                        $query->where('starts_at','>=', $startsAt)
                            ->where('ends_at', '<=', $endsAt);
                    });
                })->count() > 0){

                return response()->json(
					[
						'errors' => [
							trans('booking.errors.alreadyBooked')
						],
					],
					422);

            }

            // the booking is way to far in the future
            if ($now->diffInDays($startsAt, false) > 45){
                return response()->json(['errors' => [trans('booking.errors.toFarFuture'), ], ], 422);
            }


	        // All checks passed.
	        // Create and save the booking
	        //$booking->vatbook_id = 0;
	        $booking->station_id = $validated['station'];
	        $booking->training = $validated['training'] ? true : false;
	        $booking->voice = $validated['voice'] ? true : false;
	        $booking->event = $validated['event'] ? true : false;
	        $booking->starts_at = $startsAt;
	        $booking->ends_at = $endsAt;
	        //$booking->save();

           $res = ATCBookingsApi::editBooking($booking);

            if($res['ok']) {
                $this->account->notify(new \App\Notifications\Booking\AtcBookingUpdateNotification());
                return response()->json(['success' => true], 200);
            }
            else {
                $this->account->notify(new \App\Notifications\Booking\AtcBookingUpdateNotification());
                return response()->json(['success' => true, 'errors' => [$res['message']],], 422);
            }
		}
	}

	/**
	 * Deletes a given booking
	 *
	 * @param  Request           $request [description]
	 * @param  AtcSessionBooking $booking [description]
	 * @return [type]                     [description]
	 */
	public function delete(Request $request, AtcSessionBooking $booking)
	{
		if($request->ajax())
		{
			if($booking->controller_id === $this->account->id)
			{
                $res = ATCBookingsApi::deleteBooking($booking);
				if ($res['ok']) {
                    $this->account->notify(new \App\Notifications\Booking\AtcBookingDeletedNotification());
					return response()->json( ['success' => true ],200);
				} else {
                    return response()->json([ 'errors' => [$res['message']]],422 );
				}
			} else {
				return response()
                    ->json([ 'errors' => [ trans('booking.errors.notController'),],],422 );
			}
		}
	}
}
