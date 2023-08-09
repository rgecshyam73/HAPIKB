<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Config_curr extends Model
{
    protected $table      = 'config_curr';
    protected $primaryKey = 'curr_id';

    public $timestamps = FALSE;

    //	Relationships
    public function join_chiplists(){
    	return $this->hasMany('App\Models\Db1\Join_chiplist', 'curr_id');
    }
}
