<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Game_room extends Model
{
    protected $table 	  = 'txh_room';
    protected $primaryKey = 'room_id';

    public $timestamps = FALSE;

}
