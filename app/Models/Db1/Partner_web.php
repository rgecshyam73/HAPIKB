<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Partner_web extends Model
{
    protected $table 	  = 'partner_web';
    protected $primaryKey = 'web_id';

    public $timestamps = FALSE;

    //	Relationships
    public function partner_id(){
    	return $this->belongsTo('App\Models\PartnerId', 'partner_id');
    }

    public function partner_addrs(){
    	return $this->hasMany('App\Models\PartnerAddr', 'web_id');
    }
}
