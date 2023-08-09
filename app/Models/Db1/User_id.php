<?php

namespace App\Models\Db1;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User_id extends Authenticatable
{
    use Notifiable;

    protected $table      = 'user';
    protected $primaryKey = 'user_id';

    public $timestamps = FALSE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name', 'user_pass',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_pass'
    ];

    //  Relationships
    public function config_curr(){
        return $this->belongsTo('App\Models\Db1\Config_curr', 'curr_id');
    }    

    public function config_lang(){
        return $this->belongsTo('App\Models\Db1\Config_lang', 'lang_id');
    }    

    public function user_coins(){
        return $this->hasMany('App\Models\Db1\User_coin', 'user_id');
    }

    public function getMainBalance(){
        return $this->user_coins()->where('game_id', 0)->value('coin');
    }

    public function getLangCode(){
        return $this->config_lang()->value('code');
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return null; // not supported
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // not supported
    }
}