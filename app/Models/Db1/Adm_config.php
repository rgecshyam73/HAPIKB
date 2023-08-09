<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

use App\Interfaces\I_Adm_Config;

class Adm_config extends Model implements I_adm_config
{
    protected $table = 'adm_config';

    public $timestamps = FALSE;
}
