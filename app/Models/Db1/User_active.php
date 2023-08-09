<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class User_active extends Model
{
    protected $table      = 'user_active';
    //protected $primaryKey = ['id', 'game_id'];

    public $timestamps = FALSE;

    protected $fillable = [
        'user_id'
    ];

    public static function user(){
        return $this->belongsTo('App\Models\Db1\User_id', 'user_id');
    }
}