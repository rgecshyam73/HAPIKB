<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\{I_config_subgame};

class Config_subgame extends Model implements I_config_subgame
{
    protected $table = 'config_subgame';
    protected $primaryKey = 'subgame_id';

    function joinsubgame($game_id, $subgame_id) {
    	$queryjoin = $this->join('join_subgamestatus', function($join) use($subgame_id) {
            	$join->on('config_subgame.subgame_id', 'join_subgamestatus.subgame_id');
        })->select('join_subgamestatus.status');
    		  

    	return $queryjoin->where('config_subgame.subgame_id', $subgame_id)
                         ->where('join_subgamestatus.game_id', $game_id)
    		  ->firstOrFail();
    }
    function getTTG_unit() {
    	return $this->hasMany('App\Models\Db1\Ttg_unit', 'subgame_id');
    }

    static function getSubgame($name) {
        return constant('static::SUBGAME_' . strtoupper($name));
    }
}
