<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Game_table extends Model
{
    protected $table 	  = 'txh_table';
    protected $primaryKey = 'table_id';

    public $timestamps = FALSE;

    public function scopeGameTableRoom ($query,$sqltable){
    	$query->join($sqltable.'_room',$sqltable.'_table.room_id','=',$sqltable.'_room.room_id');
    }

    public function scopeGameTableJoinLangTable ($query,$sqltable){
    	$query->join('join_langtable','join_langtable.table_id','=',$sqltable.'_table.table_id');
    }

    public function scopeGameTableUserId ($query,$sqltable){
    	$query->leftjoin('user','user.user_id','=',$sqltable.'_table.user_id');
    }
}
