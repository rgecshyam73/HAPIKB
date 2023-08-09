<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Join_balance extends Model
{
    protected $table = 'join_balance';

    public $timestamps = FALSE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_datetime',
        'datetime',
        'user_id',
        'user_name',
        'nickname',
        'act',
        'amount',
        'tover',
        'balance',
        'trans_id',
        'game_id',
        'web_id',
        'bet_type_id',
        'user_ip',
        'period'
    ];

     protected static function boot(){
       	parent::boot();

       	static::created(function($joinBalance){
         	$array = array(
	                	'datetime' => $joinBalance->datetime,
	                	'user_id'  => $joinBalance->user_id,
	                 	'act' 	   => $joinBalance->act,
	                 	'amount'   => $joinBalance->amount,
	                 	'balance'  => $joinBalance->balance
           			 );

         	Join_lastorder::create($array);
       	});
    }
}
