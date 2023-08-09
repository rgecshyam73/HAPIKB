<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Dmc_room extends Model
{
    protected $table 	  = 'dmc_room';
    protected $primaryKey = 'room_id';

    public $timestamps = FALSE;
}
