<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Dbc_table extends Model
{
    protected $table 	  = 'dbc_table';
    protected $primaryKey = 'table_id';

    public $timestamps = FALSE;
}
