<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Game_usersit extends Model
{
    protected $table 	  = 'txh_usersit';
    protected $primaryKey = 'user_id';

    public $timestamps = FALSE;
}
