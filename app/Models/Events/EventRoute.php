<?php
namespace App\Models\Events;

class EventRoute extends \Illuminate\Database\Eloquent\Model
{
    protected $table = "event_routes";

    protected $dates = [
        'begins_at',
        'ends_at',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'joined_by_me',
    ];


    public function getJoinedByMeAttribute()
    {
        if (!auth()->check()) return false;
        if ($this->legs()->count() == 0) return false;
        return $this->legs()->first()->accounts()->wherePivot('account_id', auth()->id())->exists(); // a little hacky
    }

    public function legs()
    {
        return $this->hasMany(
            \App\Models\Events\RouteLeg::class,
            'route_id',
            'id'
        );
    }
}
