<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Dmb_table extends Model
{
    protected $table 	  = 'dmb_table';
    protected $primaryKey = 'table_id';

    public $timestamps = FALSE;
}
