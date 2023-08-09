<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ViewPartnerGame extends Model
{
    protected $table = 'rv_partner_game';

    public $timestamps = FALSE;

    public static function scopeIsPartner($query){
    	//return $query->where(['web_id'=>Auth::user()->web_id]);
    }

    public static function scopeActive($query){
    	return $query->isPartner()->where(['status_game'=>1, 'status_partner'=>1]);
    }

    public static function scopeActive2($query){
    	return $query->isPartner()->where('status_game','>',0)->where('status_partner',1);
    }

    public static function scopeActive3($query){
        return $query->isPartner()->where('status_game','>',0);
    }
}
