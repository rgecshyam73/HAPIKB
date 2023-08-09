<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Join_langroom extends Model
{
    protected $table 	  = 'join_langroom';
    protected $primaryKey = 'id';

    public $timestamps = FALSE;

    public function scopeGameRoom($query,$sqltable){
    	$query
    		->join($sqltable.'_room','join_langroom.room_id','=',$sqltable.'_room.room_id');
    }
}
