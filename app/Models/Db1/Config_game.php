<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

use App\Interfaces\I_config_game;

class Config_game extends Model implements I_config_game
{
    protected $table 	  = 'config_game';
    protected $primaryKey = 'game_id';

    public $timestamps = FALSE;

    //	Relationships
    public function config_bet_types() {
        return $this->hasMany('App\Models\Db1\Config_bet_type', 'game_id');
    }

    public function ttg_config() {
    	return $this->hasMany('App\Models\Db1\Ttg_config', 'game_id');
    }

    public function ttg_unit() {
        return $this->hasMany('App\Models\Db1\Ttg_unit', 'game_id');
    }

    public function adm_servers() {
        return $this->hasMany('App\Models\Db1\Adm_server', 'game_id');
    }

    public function ddc_rooms() {
        return $this->hasMany('App\Models\Db1\Ddc_room', 'game_id');
    }

    public function join_subgames() {
        return $this->hasMany('App\Models\Db1\Join_subgamestatus', 'game_id');
    }

    public function getGameList($web_id) {
        return Config_game::join('partner_game', function($join) use ($web_id) {
                            $join->on('partner_game.game_id', 'config_game.game_id')
                                 ->where('partner_game.status', config('global.config_game.status.active'))
                                 ->where('partner_game.web_id', $web_id);
                            })->select('config_game.game_id', 'config_game.game_code', 'config_game.game_name', 'config_game.type', 'config_game.sqltable')
                              ->where('config_game.status', '>', Config_game::STATUS_CLOSE)
                                                    ->groupBy('type')
                                                    ->orderBy('type', 'ASC');
    }

    public function getLotteryGameList() {
        return Config_game::join('ttg_config', function($join) {
                            $join->on('config_game.game_id', 'ttg_config.game_id')
                                 ->where('ttg_config.name', 'market_status');
                            })->select('config_game.game_id', 'config_game.game_code', 'config_game.game_name', 'config_game.type', 'config_game.sqltable', 'ttg_config.value')
                              ->get();
    }
}
