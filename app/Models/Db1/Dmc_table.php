<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Dmc_table extends Model
{
    protected $table 	  = 'dmc_table';
    protected $primaryKey = 'table_id';

    public $timestamps = FALSE;
}
