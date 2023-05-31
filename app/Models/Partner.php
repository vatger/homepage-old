<?php

namespace App\Models;

use App\Models\Membership\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Partner extends Model
{

    protected $table = 'partners';

    protected $fillable = [
        'created_by',
        'name',
        'logo_url',
        'link_url',
        'description_de',
        'description_en'
    ];

    public function user()
    {
        return $this->belongsTo(Account::class, 'id', 'created_by');
    }

    public function getDescriptionAttribute()
    {
        if (
            (Auth::check() && Auth::user()->settings->language == 'de') ||
            ((Session::has('language')) && Session::get('language') == 'de')
        )
        {
            return $this->description_de;
        } else
        {
            return $this->description_en;
        }
    }
}
