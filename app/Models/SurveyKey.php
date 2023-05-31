<?php

namespace App\Models;

use App\Models\Membership\Account;
use Illuminate\Database\Eloquent\Model;

class SurveyKey extends Model
{
    protected $table = 'survey_keys';
    protected $casts = [
        'valid_till' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'id', 'user_id');
    }
}
