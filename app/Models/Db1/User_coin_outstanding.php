<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class User_coin_outstanding extends Model
{
    protected $table      = 'user_coin_outstanding';
    protected $primaryKey = 'user_id';

    public $timestamps = FALSE;
}