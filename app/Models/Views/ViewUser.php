<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class ViewUser extends Model
{
	protected $connection = 'mysql-write';
    protected $table = 'rv_user';
    protected $primaryKey = 'user_id';

    public $timestamps = FALSE;
}
