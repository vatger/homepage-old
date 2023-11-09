<?php

namespace App\Http\Controllers\Bookings;


use App\Http\Controllers\Controller;
use App\Libraries\ATCBookingsApi;
use Illuminate\Http\Request;
use App\Models\Booking\AtcSessionBooking;
use Carbon\Carbon;

class AtcBookingController extends Controller
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
        abort(422, 'disabled');
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
        abort(422, 'disabled');
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
