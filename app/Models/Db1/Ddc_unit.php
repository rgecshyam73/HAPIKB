<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

use App\Interfaces\I_ddc_unit;

class Ddc_unit extends Model implements I_ddc_unit
{
    protected $table = 'ddc_unit';

    public $timestamps = FALSE;
}
