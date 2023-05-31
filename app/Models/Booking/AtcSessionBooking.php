<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;

class AtcSessionBooking extends Model
{

	// use LogsActivity;
    
    protected $table = 'bookings_atc';

    protected $dates = [
        'starts_at',
        'ends_at',
        'created_at',
        'updated_at',
    ];

    protected static $logAttributes = ['*'];

    // protected $appends = [
    // 	'station'
    // ];

    /**
     * The ATS station that has been booked.
     *
     * @return [type] [description]
     */
    public function station()
    {
        return $this->belongsTo(
        		\App\Models\Navigation\Station::class,
        		'station_id',
        		'id'
        	);
    }

    /**
     * The user that made the booking.
     *
     * @return [type] [description]
     */
    public function controller()
    {
        return $this->belongsTo(
	        	\App\Models\Membership\Account::class,
	        	'controller_id',
	        	'id'
	        )->select(['id', 'firstname', 'lastname']);
    }

    /**
     * Get all bookings for a given event
     * 
     * @param  [type] $query    [description]
     * @param  [type] $event_id [description]
     * @return [type]           [description]
     */
    public function scopeForEvent($query, $event_id)
    {
    	return $query->where('event', true)
    				->where('event_id', $event_id);
    }

    /**
     * All bookings an account has made
     * 
     * @param  [type] $query [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function scopeForAccountId($query, $id)
    {
        return $query->where('controller_id', $id);
    }

    /**
     * All bookings for a given station id
     * 
     * @param  [type] $query [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function scopeForStation($query, $id)
    {
        return $query->where('station_id', $id);
    }
}
