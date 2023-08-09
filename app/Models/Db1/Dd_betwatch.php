<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Dd_betwatch extends Model
{
    public $timestamps = FALSE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['period', 'curr_id', 'bet_type_id', 'bet', 'amount'];
}
