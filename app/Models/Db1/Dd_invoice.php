<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Dd_invoice extends Model
{
    public $timestamps = FALSE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['date', 'period', 'user_id', 'curr_id', 'total', 'host_id'];

    //	Relationships
    public function dd_bets() {
    	return $this->hasMany('App\Models\Db1\Dd_bet', 'invoice_id');
    }
}
