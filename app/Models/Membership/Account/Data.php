<?php

namespace App\Models\Membership\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Membership\Account;
use Cache;

class Data extends Model
{
    use SoftDeletes;

    /**
     * The name of the database table.
     *
     * @var string
     */
    protected $table = 'membership_account_data';

    /**
     * The primary key of the table.
     *
     * @var string
     */
    protected $primaryKey = 'account_id';

    /**
     * These fields are converted to Carbon\Carbon objects.
     *
     * @var \Carbon\Carbon
     */
    public $dates = ['deleted_at', 'registered_at'];

    /**
     * These fields are mass assignable.
     *
     * @var array
     */
    public $fillable = ['account_id'];

    /**
     * Automagically appended to the model
     * @var [type]
     */
    public $appends = [
        'controllerRating',
        'pilotRating',
    ];

    /**
     * The account reference this is connected to.
     *
     * @return \App\Models\Membership\Account | null
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function getControllerRatingAttribute()
    {
        $atdRating = $this->rating_atc;
        return Cache::remember(
            'rating_atc_'.$this->rating_atc,
            60*60*48,
            function () use ($atdRating) {
                $ratings_atd = [
                    -1 => [
                        'short' => 'Inactive',
                        'long' => 'Inactive',
                        'grp' => 'Inactive',
                    ],
                    0 => [
                        'short' => 'Suspended',
                        'long' => 'Suspended',
                        'grp' => 'Suspended',
                    ],
                    1 => [
                        'short' => 'OBS',
                        'long' => 'Observer',
                        'grp' => 'Observer',
                    ],
                    2 => [
                        'short' => 'S1',
                        'long' => 'Student',
                        'grp' => 'Student',
                    ],
                    3 => [
                        'short' => 'S2',
                        'long' => 'Student 2',
                        'grp' => 'Student',
                    ],
                    4 => [
                        'short' => 'S3',
                        'long' => 'Senior Student',
                        'grp' => 'Student',
                    ],
                    5 => [
                        'short' => 'C1',
                        'long' => 'Controller',
                        'grp' => 'Controller',
                    ],
                    6 => [
                        'short' => 'C2',
                        'long' => 'Controller 2',
                        'grp' => 'Controller',
                        'active' => false,
                    ],
                    7 => [
                        'short' => 'C3',
                        'long' => 'Senior Controller',
                        'grp' => 'Controller',
                    ],
                    8 => [
                        'short' => 'I1',
                        'long' => 'Instructor',
                        'grp' => 'Instructor',
                    ],
                    9 => [
                        'short' => 'I2',
                        'long' => 'Instructor 2',
                        'grp' => 'Instructor',
                        'active' => false,
                    ],
                    10 => [
                        'short' => 'I3',
                        'long' => 'Senior Instructor',
                        'grp' => 'Instructor',
                    ],
                    11 => [
                        'short' => 'SUP',
                        'long' => 'Supervisor',
                        'grp' => 'Supervisor',
                    ],
                    12 => [
                        'short' => 'ADM',
                        'long' => 'Administrator',
                        'grp' => 'Administrator',
                    ],
                ];

                if(array_key_exists($atdRating, $ratings_atd)) {
                    return $ratings_atd[$atdRating];
                }
            }
        );
    }

    public function getPilotRatingAttribute()
    {
        $ptdRating = $this->rating_pilot;
        return Cache::remember(
            'rating_ptd_'.$this->rating_pilot,
            60*60*48,
            function () use ($ptdRating) {
                $rating = '';
                switch ($ptdRating) {
                    case 0:
                        $rating = 'P0';
                        break;
                    case 1:
                        $rating = 'P1';
                        break;
                    case 3:
                        $rating = 'P1 & P2';
                        break;
                    case 7:
                        $rating = 'P1 & P2 & P3';
                        break;
                    case 13:
                        $rating = 'No P0'; //why vatsim connect?
                        break;
                    case 15:
                        $rating = 'P1 & P2 & P3 & P4';
                        break;
                    default:
                        $rating = 'Unkown';
                        break;
                }

                return $rating;
            }
        );
    }
}
