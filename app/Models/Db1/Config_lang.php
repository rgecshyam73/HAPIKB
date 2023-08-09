<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Config_lang extends Model
{
    protected $table 	  = 'config_lang';
    protected $primaryKey = 'lang_id';

    public $timestamps = FALSE;
}
