<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

use App\Interfaces\I_config_bet_type;

class Config_bet_type extends Model implements I_config_bet_type
{
	protected $table      = 'config_bet_type';
	protected $primaryKey = 'bet_type_id';

    public $timestamps = FALSE;

    static function setPosisi($items) {
        return constant('static::BET_TYPE_2D_'.$items);
    }

    static function getBetType($name) {
        return constant('static::BET_TYPE_' . strtoupper($name));
    }
}
