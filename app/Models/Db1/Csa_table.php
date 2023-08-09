<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Csa_table extends Model
{
    protected $table 	  = 'csa_table';
    protected $primaryKey = 'table_id';

    public $timestamps = FALSE;
}
