<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Ddj_table extends Model
{
    protected $table 	  = 'djh_table';
    protected $primaryKey = 'table_id';

    public $timestamps = FALSE;
}
