<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class User_coin extends Model
{
	protected $connection = 'mysql-write';
    protected $table      = 'user_coin';
    protected $primaryKey = 'user_id';

    public $timestamps = FALSE;

    public function scopeGameUserSitUserId ($query,$sqltable){
    	$query
    		->join($sqltable.'_usersit','user_coin.user_id','=',$sqltable.'_usersit.user_id')
    		->join('user',$sqltable.'_usersit.user_id','=','user.user_id');
    }
}