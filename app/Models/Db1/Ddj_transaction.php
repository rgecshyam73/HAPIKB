<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Ddj_transaction extends Model
{
    protected $table 	  = 'djh_transaction';
    protected $primaryKey = 'id';

    public $timestamps = FALSE;
}
