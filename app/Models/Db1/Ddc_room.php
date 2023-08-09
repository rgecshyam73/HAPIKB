<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

use App\Interfaces\I_ddc_room;

class Ddc_room extends Model implements I_ddc_room
{
    protected $table 	  = 'ddc_room';
    protected $primaryKey = 'room_id';

 	public $timestamps = FALSE;

 	//	Relationships
 	public function config_stream() {
 	    return $this->belongsTo('App\Models\Db1\Config_stream', 'stream_id');
 	}

 	public function config_game() {
 		return $this->belongsTo('App\Models\Db1\Config_game', 'game_id');
 	}

 	public function ddc_configs() {
 		return $this->hasMany('App\Models\Db1\Ddc_config', 'room_id');
 	}

 	public function ddc_limits() {
 		return $this->hasMany('App\Models\Db1\Ddc_limit', 'room_id');
 	}

 	public function dd_invoices() {
 		return $this->hasMany('App\Models\Db1\Dd_invoice', 'room_id');
 	}

 	public function dd_numbers() {
 		return $this->hasMany('App\Models\Db1\Dd_number', 'room_id');
 	}
}
