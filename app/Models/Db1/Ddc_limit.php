<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Ddc_limit extends Model
{
    protected $table = 'ddc_limit';

    public $timestamps = FALSE;

    //	Relationships
    public function ddc_units() {
    	return $this->hasMany('App\Models\Db1\Ddc_unit', 'limit_id');
    }

    public function ddc_room() {
    	return $this->belongsTo('App\Models\Db1\Ddc_room', 'room_id');
    }
}
