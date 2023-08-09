<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class ViewPartner extends Model
{
    protected $table = 'rv_partner';

    public $timestamps = FALSE;

    public function isValid()
    {
        return $this->status > 0;
    }
}
