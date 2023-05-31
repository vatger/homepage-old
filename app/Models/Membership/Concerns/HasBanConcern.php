<?php

namespace App\Models\Membership\Concerns;

use App\Models\Membership\Account\Ban;
use Carbon\Carbon;

trait HasBanConcern{

    public function hasBans()
    {
        return Ban::where('account_id', $this->id)->exists();
    }

    public function bans()
    {
        return $this->hasMany(Ban::class)->orderBy('created_at', 'DESC');
    }

    public function getIsCurrentlyBannedAttribute()
    {
    	$now = Carbon::now()->utc();
        return $this->bans()->where(function($query) use ($now) {
            $query->where('permanent', true)->orWhere('banned_till', '>=', $now);
        })->exists();
    }

    public function getIsCurrentlyTSBannedAttribute()
    {
        $now = Carbon::now()->utc();
        return $this->bans()->where(function($query) use ($now) {
            $query->where('permanent', true)->orWhere('banned_till', '>=', $now);
        })->where('teamspeak', 1)->exists();
    }

    public function getIsCurrentlyHomepageBannedAttribute()
    {
        $now = Carbon::now()->utc();
        return $this->bans()->where(function($query) use ($now) {
            $query->where('permanent', true)->orWhere('banned_till', '>=', $now);
        })->where('homepage', 1)->exists();
    }

    public function getCurrentBanAttribute()
    {
        $now = Carbon::now()->utc();

        return $this->bans()->where(function($query) use ($now) {
            $query->where('permanent', true)->orWhere('banned_till', '>=', $now);
        })->first();
    }

}
