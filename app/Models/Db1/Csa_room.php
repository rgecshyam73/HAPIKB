<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Csa_room extends Model
{
    protected $table 	  = 'csa_room';
    protected $primaryKey = 'room_id';

    public $timestamps = FALSE;
}
