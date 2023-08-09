<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Adm_transaction_day extends Model
{
    protected $table = 'adm_transaction_day';

    protected $fillable = ['created_date','user_name','cash_in','cash_out','curr_id','web_id','user_id'];

    public $timestamps = FALSE;
}
