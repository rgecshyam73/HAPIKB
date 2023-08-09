<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

class Join_transaction_day extends Model
{	

	protected $fillable = [
		'transfer_out'
    ];

    protected $table 	  = 'join_transaction_day';

    public $timestamps = FALSE;
}
