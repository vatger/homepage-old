<?php

namespace App\Models\Membership\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Membership\Account;

class Setting extends Model
{
    use SoftDeletes;

    /**
     * The name of the database table.
     *
     * @var string
     */
    protected $table = 'membership_account_settings';

    /**
     * The primary key of the table.
     *
     * @var string
     */
    protected $primaryKey = 'account_id';

    /**
     * Carbon date conversion of these fields.
     *
     * @var array
     */
    public $dates = ['deleted_at'];

    /**
     * Mass asignable fields.
     *
     * @var array
     */
    public $fillable = ['account_id'];

    /**
     * The account this is bound to.
     *
     * @return \App\Models\Membership\Account | null
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
