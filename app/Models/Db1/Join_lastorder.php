<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Join_lastorder extends Model
{
    protected $table = 'join_lastorder';

    public $timestamps = FALSE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'datetime',
        'user_id',
        'act',
        'amount',
        'balance'
    ];
}
