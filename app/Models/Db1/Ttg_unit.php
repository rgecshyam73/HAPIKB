<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\I_ttg_unit;

class Ttg_unit extends Model implements I_ttg_unit
{
	protected $table = 'ttg_unit';
}
