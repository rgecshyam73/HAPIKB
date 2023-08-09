<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Dd_bet extends Model
{
    public $timestamps = FALSE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['invoice_id', 'bet_type_id', 'bet', 'amount', 'disc', 'prize', 'status'];

    //	Relationships
    public function dd_bet_details(){
    	return $this->hasMany('App\Models\Db1\Dd_bet_detail', 'bet_id');
    }

    public function config_bet_type(){
        return $this->hasOne('App\Models\Db1\Config_bet_type', 'bet_type_id');
    }
}
